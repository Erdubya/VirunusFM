<?php
/**
 * User: Erik Wilson
 * Date: 31-Dec-16
 * Time: 17:35
 * Authenticates a client for use by a user.  Sends a non-expiring JWT to the
 * client for unfettered access to a users data.
 */

require_once "../_config.php";
use Firebase\JWT\JWT;

$dbh = db_connect() or die(DB_CONNERR);
$username = $_POST['username'];
$password = $_POST['password'];
$client   = $_POST['api_key'];

// Authenticate user
$sql = "SELECT password, user_id FROM users WHERE username = $username";
if ($result = $dbh->query($sql)) {
	$user_result = $result->fetch();
	$user_id     = $user_result['user_id'];
	$pswd        = password_verify($password, $user_result['password']);
} else {
	$pswd = false;
}

// Validate client's API key
$sql = "SELECT active, expires FROM clients WHERE api_key = $client";
if ($result = $dbh->query($sql)) {
	$result = $result->fetch();
	$clnt   = $result['active'] && (strtotime($result['expires']) > time());
} else {
	$clnt = false;
}

if ($pswd && $clnt) {
	// mcrypt is deprecated as of PHP 7.1.  This function will have to be
	//  changed in the future.
	$token_id    = base64_encode(mcrypt_create_iv(32));
	$issued_at   = time();
	$server_name = "http://" . $_SERVER['SERVER_NAME'];
	$expires     = strtotime($result['expires']) - time();

	/** @noinspection PhpUndefinedVariableInspection */
	$data = [
		'iat'  => $issued_at,
		'jti'  => $token_id,
		'iss'  => $server_name,
		'exp'  => $expires,
		'data' => [
			'user_id' =>  $user_id,
			'username' => $username,
			'client'   => $client
		]
	];

	$jwt = JWT::encode($data, JWT_KEY, 'HS512');

	echo json_encode(['jwt' => $jwt]);
} else {
	echo json_encode(['error' => "invalid information"]);
}


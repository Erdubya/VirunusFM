<?php
/**
 * User: Erik Wilson
 * Date: 31-Dec-16
 * Time: 17:35
 * 
 * Authenticates a client for use by a user.  Sends a non-expiring JWT to the
 * client for unfettered access to a users data.
 */

require_once "../_config.php";
global $config;

$dbh = db_connect() or die($config->database->errors->connect);
$username = $_POST['username'];
$password = $_POST['password'];
$client = $_POST['api_key'];

// Authenticate user
$sql = "SELECT password FROM users WHERE username = $username";
if ($result = $dbh->query($sql)) {
	$pswd = password_verify($password, $result->fetch()['password']);
} else {
	$pswd = false;
}

// Validate client's API key
$sql = "SELECT active, expires FROM clients WHERE api_key = $client";
if ($result = $dbh->query($sql)) {
	$result = $result->fetch();
	$clnt = $result['active'] && (strtotime($result['expires']) > time());
} else {
	$clnt = false;
}

if ($pswd && $clnt) {
	// mcrypt is deprecated as of PHP 7.1.  This function will have to be
	//  changed in the future.
	$token_id = base64_encode(mcrypt_create_iv(32));
	$issued_at = time();
	$server_name = $config->webhost;
}


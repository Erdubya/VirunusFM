<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 14:25
 * This is the main API file for VirunusFM.  Calls for the web service will
 * generate from here following an http request.
 */
require_once "../_config.php";
include "read.php";
include "response.php";
use \Firebase\JWT\JWT;

$dbh = db_connect() or die(DB_CONNERR);
unset($auth_err);
$response = new Response();

$method = $_POST['method'];
$data = $_POST['data'];

// Validate API and authenticate user.
$jwt = $data['token'];
try {
	$token = JWT::decode($jwt, 'secret', ['HS256']);
	
	$user_id = $token['user_id'];
	$client = $token['client'];
	
	//TODO: implement response handlers.
} catch (\Firebase\JWT\BeforeValidException $e) {
	echo json_encode($e->getMessage());
} catch (\Firebase\JWT\ExpiredException $e) {
	echo json_encode($e->getMessage());
} catch (\Firebase\JWT\SignatureInvalidException $e) {
	echo json_encode($e->getMessage());
} catch (UnexpectedValueException $e) {
	echo json_encode($e->getMessage());
}

switch (strtolower($method)) {
	case "write":
		write($data['listens'], $user_id, $client);
		break;
	case "read":
		read(new Read());
		break;
	default:
		$response->set_error(Errors::METHOD);
		break;
}

header("Content-type: application/json");
$response->display();

/**
 * @param mixed $row
 *
 * @return bool
 */
function check_client($row)
{
	if (is_a($row, "boolean")) {
		return false;
	} else if (is_null($row)) {
		return false;
	} else if ( !$row['active']) {
		return false;
	} else {
		return (strtotime($row['expires']) > time());
	}
}

function write($listens, $user, $client)
{
	$result = array('Y' => 0, 'N' => 0);
	foreach ($listens as $listen) {
		if (
			array_key_exists('artist', $listen)
			&& array_key_exists('track', $listen)
			&& array_key_exists('album', $listen)
			&& array_key_exists('datetime', $listen)
		) {
			list($artist, $track, $album, $datetime) = $listen;
			
			//TODO: lookup data in tables; record track.
		}

	}
}

/**
 * @param Read $read
 */
function read($read)
{

}

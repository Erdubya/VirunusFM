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
include "write.php";
include "response.php";
use \Firebase\JWT\JWT;

$dbh = db_connect() or die(DB_CONNERR);
unset($auth_err);
$response = new Response();

// Get API data from DB.
//$api = $_POST['api_key'];
//$sql = "SELECT * FROM clients where api_key = $api";
//$row = $dbh->query($sql);
//if ($row !== false) {
//	$row = $row->fetch();
//} else {
//	$response->set_error(Errors::API);
//}

header("Content-type: application/json");

// Validate API and authenticate user.
$jwt = $_POST['token'];
try {
	$token = JWT::decode($jwt, JWT_KEY, [ 'HS256' ]);
	echo json_encode(['token' => $token]);
} catch (Exception $e) {
	echo json_encode(['error' => $e->getMessage()]);
}

//if (isset($auth_err)) {
//	echo json_encode($auth_err);
//	die();
//}

//echo json_encode($_POST);
die();

switch (strtolower($_POST["method"])) {
	case "write":
		if (!write(new Write())) {
			$response->set_error(Errors::DATA);
		}
		break;
	case "read":
		read(new Read());
		break;
	default:
		$response->set_error(Errors::METHOD);
		break;
}

$methods = array_keys($_POST);
echo json_encode($_POST);

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
	} else if ( ! $row['active']) {
		return false;
	} else {
		return ( strtotime($row['expires']) > time() );
	}
}

/**
 * @param Write $write
 * 
 * @return bool
 */
function write($write)
{
	$keys = array_keys($_POST);
	if (!$write->check_data($keys)) {
		return false;
	}
	
	$write->setArtist($_POST["artist"]);
	$write->setTrack($_POST["track"]);
	$write->setAlbum($_POST["album"]);
	$write->setDatetime($_POST["datetime"]);
	$write->listen();
	return true;
}

/**
 * @param Read $read
 */
function read($read)
{
	
}

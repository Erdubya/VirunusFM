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

$dbh = db_connect() or die(DB_CONNERR);
unset($auth_err);
$response = new Response();

// Get API data from DB.
$api = $_POST['api_key'];
$sql = "SELECT * FROM clients where api_key = $api";
$row = $dbh->query($sql);
if ($row !== false) {
	$row = $row->fetch();
} else {
	$response->set_error(Errors::API);
}

// Validate API and authenticate user.
if (checkAPI($row)) {
	if (isset($_POST['username'])) {
		$sql      = "SELECT password FROM users WHERE username = "
		            . $_POST['username'];
		$user_row = $dbh->query($sql)->fetch();

		if (is_null($user_row)) {
			$auth_err = 2;
		} else if ( ! password_verify($_POST["password"],
			$user_row["password"])
		) {
			$auth_err = 2;
		}
	} else {
		$auth_err = 2;
	}
} else {
	$auth_err = 1;
}

header("Content-type: application/json");
if (isset($auth_err)) {
	echo json_encode($auth_err);
	die();
}

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
function checkAPI($row)
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
	
}

/**
 * @param Read $read
 */
function read($read)
{
	
}

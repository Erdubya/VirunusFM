<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 14:25
 * This is the main API file for VirunusFM.  Calls for the web service will
 * generate from here following an http request.
 */
require_once( "../_config.php" );

$dbh = db_connect() or die(DB_CONNERR);
$api = $_POST['api_key'];
$sql = "SELECT * FROM client where api_key = $api";
$row = $dbh->query($sql)->fetch();

if (checkAPI($row)) {
	
	if (isset($_POST['username'])) {
		$sql = "SELECT * FROM \"user\" WHERE \"user\".username = " . $_POST['username'];
		$user_row = $dbh->query($sql)->fetch();
		
		password_verify($_POST["password"], $user_row["password"]);
	}
	
}

function checkAPI($row)
{
	if (is_null($row)) {
		return false;
	} else if(!$row['active']) {
		return false;
	} else {
		return (strtotime($row['expires']) > time());
	}
}

function authenticate($username, $password, $row)
{
	password_verify($password, $row['password']);
}

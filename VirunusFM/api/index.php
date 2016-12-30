<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 14:25
 * This is the main API file for VirunusFM.  Calls for the web service will
 * generate from here following an http request.
 */
require_once( "../_config.php" );

if (isset($_POST['api_key'])) {

	if (isset($_POST['username'])) {
		authenticate($_POST['username'], $_POST['password']);
	}

	function authenticate($username, $password)
	{
		password_hash($password, PASSWORD_DEFAULT);
	}
}

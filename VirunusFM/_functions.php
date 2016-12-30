<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 17:35
 */

function db_connect()
{
	try {
		$dbh = new PDO("pgsql:host=" . DB_HOSTNAME . ";dbname=" . DB_DATABASE,
			DB_USERNAME, DB_PASSWORD);
	} catch (PDOException $e) {
		$dbh = null;
	}

	return $dbh;
}

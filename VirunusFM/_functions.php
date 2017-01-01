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

function var_dump_to_str($var) {
	ob_start();
	var_dump($var);
	return ob_get_clean();
}

abstract class Errors extends BasicEnum {
	const API = 1;
	const AUTH = 2;
	const METHOD = 3;
	const DATA = 4;
}

abstract class BasicEnum {
	private static $constCacheArray = null;

	public static function isValidName($name, $strict = false)
	{
		$constants = self::getConstants();

		if ($strict) {
			return array_key_exists($name, $constants);
		}

		$keys = array_map('strtolower', array_keys($constants));

		return in_array(strtolower($name), $keys);
	}

	private static function getConstants()
	{
		if (self::$constCacheArray == null) {
			self::$constCacheArray = [];
		}
		$calledClass = get_called_class();
		if ( !array_key_exists($calledClass, self::$constCacheArray)) {
			$reflect                               = new ReflectionClass($calledClass);
			self::$constCacheArray[ $calledClass ] = $reflect->getConstants();
		}

		return self::$constCacheArray[ $calledClass ];
	}

	public static function isValidValue($value, $strict = true)
	{
		$values = array_values(self::getConstants());

		return in_array($value, $values, $strict);
	}
}

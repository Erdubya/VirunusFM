<?php

require_once "vendor/autoload.php";

$config_array = array(
	'webhost' => 'www.virunus.com',
	'database' => [
		'adapter' => 'pdo_pgsql',
		'params' => [
			'host' => 'localhost',
			'username' => 'virunus',
			'password' => 'music@work',
			'dbname' => 'virunus_fm'
		],
		'errors' => [
			'connect' => "Cannot connect to database!"
		]
	],
	"jwt_secret" => "F9X6acLbbc/eD8vkatOUUpWUVBVz6V1ewV88NlkzZOR2/rzO2O1fRXKUuFUjSn1oRuRHJD0O2fgCT2GrCDKVag==",
);

$config = new Zend\Config\Config($config_array);

require_once "_functions.php";

<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 14:14
 */
require_once "../_config.php";
global $config;
var_dump($config);

$url = "http://api.virun.us:8080";
$auth = "39802830831bed188884e193d8465226";
$listens = array();

for ($i = 0; $i < 10; $i++) {
	$listen = array(
		"artist"   => "a" . $i,
		"track"    => 't' . $i,
		"album"    => 'b' . $i,
		"datetime" => 'd' . $i
	);
	array_push($listens, $listen);
}

$object = array("auth" => $auth, "listens" => $listens);

$options = array(
	'http' => array(
		'header' => "Content-type: application/x-www-form-urlencoded\r\n",
		'method' => 'POST',
		'content' => http_build_query($object)
	)
);

$context = stream_context_create($options);
$result = file_get_contents($url, true, $context);

if ($result === FALSE) {
	echo false;
} else {
//	echo $result;
//	echo json_decode($result);
	var_dump($result);
	var_dump(json_decode($result));
}




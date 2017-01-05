<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 14:14
 */
require_once "../_config.php";
$url  = "http://api.virun.us:8080";
$auth = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJjbGllbnQiOiIxMjM0NTY3ODkwIiwidXNlcm5hbWUiOiJKb2huIERvZSIsInVzZXJfaWQiOjF9.pglSb5wysPdXLl3KsIBF0_ciykhYZ0Heo2SnMO5GpBA";

$listens = array();
for ($i = 0; $i < 1; $i ++) {
	$listen = array(
		"artist"   => "a" . $i,
		"track"    => 't' . $i,
		"album"    => 'b' . $i,
		"datetime" => 'd' . $i
	);
	array_push($listens, $listen);
}
$write_data = array("token" => $auth, "listens" => $listens);
$write      = ['method' => 'write', 'data' => $write_data];

$read_data = ['token' => $auth, 'count' => 20];
$read      = ['method' => 'read', 'data' => $read_data];

$options = array(
	'http' => array(
		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'method'  => 'POST',
		'content' => http_build_query($write)
	)
);

$context = stream_context_create($options);
$result  = file_get_contents($url, true, $context);

if ($result === false) {
	echo false;
} else {
//	echo $result;
//	echo json_decode($result);
	echo "<pre>", print_r($result), "</pre>";
	echo "<pre>", print_r(json_decode($result, true)), "</pre>";
}




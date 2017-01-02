<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 14:14
 */
require_once "../_config.php";
$url     = "http://api.virun.us:8080";
$auth    = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ";
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

$object = array("token" => $auth, "listens" => $listens);

$options = array(
	'http' => array(
		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'method'  => 'POST',
		'content' => http_build_query(['method' =>'write', 'data' => $object])
	)
);

$context = stream_context_create($options);
$result  = file_get_contents($url, true, $context);

if ($result === false) {
	echo false;
} else {
//	echo $result;
//	echo json_decode($result);
	var_dump($result);
	echo "<br/>";
	var_dump(json_decode($result, true));
}




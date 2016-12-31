<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 14:14
 */
$url = "http://api.virun.us:8080";
$data = array('api_key' => '9af4d8381781baccb0f915e554f8798d',
              'method' => 'read',
              'user' => 'virunus', 
              'count' => 20);

$options = array(
	'http' => array(
		'header' => "Content-type: application/x-www-form-urlencoded\r\n",
		'method' => 'POST',
		'content' => http_build_query($data)
	)
);

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
	echo false;
} else {
	echo $result;
}




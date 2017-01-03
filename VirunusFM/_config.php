<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 17:29
 */

require_once "_functions.php";
require_once "vendor/autoload.php";
error_reporting(E_ALL);


define("DB_CONNERR", "Cannot connect to database!");
define("DB_USERNAME", "virunus");
define("DB_PASSWORD", "music@work");
define("DB_HOSTNAME", "localhost");
define("DB_DATABASE", "virunus_fm");

define("API_URI", "api.virun.us:8080");

define("JWT_KEY", "secret");

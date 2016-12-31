<?php
/**
 * User: Erik Wilson
 * Date: 31-Dec-16
 * Time: 12:09
 * This object holds the data necessary in a JSON response.  The object will be
 * built by the main API handler, JSON encoded, and returned via HTTP.
 */
require_once "../_config.php";

class Response {
	private $artist;
	private $track;
	private $album;
	private $datetime;
	/**
	 * @var int
	 */
	private $error;

	/**
	 * @param int $error
	 */
	public function set_error($error) {
		$this->error = $error;
	}

}



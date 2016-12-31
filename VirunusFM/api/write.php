<?php
/**
 * User: Erik Wilson
 * Date: 31-Dec-16
 * Time: 11:53
 */
require_once "../_config.php";

class Write {
	/**
	 * @var string
	 */
	private $artist;
	/**
	 * @var string
	 */
	private $track;
	/**
	 * @var string
	 */
	private $album;
	/**
	 * @var string
	 */
	private $datetime;

	/**
	 * @param string $artist
	 */
	public function setArtist($artist)
	{
		$this->artist = $artist;
	}

	/**
	 * @param string $track
	 */
	public function setTrack($track)
	{
		$this->track = $track;
	}

	/**
	 * @param string $album
	 */
	public function setAlbum($album)
	{
		$this->album = $album;
	}

	/**
	 * @param string $datetime
	 */
	public function setDatetime($datetime)
	{
		$this->datetime = $datetime;
	}
	
	/**
	 * @param array $keys
	 * 
	 * @return boolean
	 */
	public function check_data($keys) {
		 return (in_array("artist", $keys)
		    && in_array("track", $keys) 
		    && in_array("album", $keys)
			&& in_array("datetime", $keys));
	}
	
	public function listen() {
		
	}
}

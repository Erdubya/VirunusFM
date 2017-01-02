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
	/**
	 * @var string
	 */
	private $client;

	/**
	 * @var string
	 */
	private $method;
	
	/**
	 * @var array
	 */
	private $user = [];
	
	/**
	 * @var array
	 */
	private $errors = [];

	/**
	 * @var array
	 */
	private $listens = [];


	/**
	 * Returns the api_key of the client.
	 * @param string $client
	 */
	public function setClient($client)
	{
		$this->client = $client;
	}
	
	public function setMethod($method)
	{
		$this->method = $method;
	}
	/**
	 * @param int $user_id
	 * @param string $username
	 */
	public function setUser($user_id, $username)
	{
		$this->user[0] = $user_id;
		$this->user[1] = $username;
	}

	/**
	 * Returns an array containing the UserID as the first element, and the
	 * username as the second.
	 * @return string
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @param int $error
	 */
	public function add_error($error)
	{
		array_push($this->errors, $error);
	}

	/**
	 * @param array $listen
	 */
	public function add_listen($listen) {
		array_push($this->listens, $listen);
	}

	public function respond()
	{
		$response = json_encode(['VirunusFM' => $this->build_array()]);
		echo $response;
	}
	
	private function build_array() {
		$response = [];
		$response['method'] = $this->method;
		$response['listens'] = $this->listens;
		return $response;
	}

}



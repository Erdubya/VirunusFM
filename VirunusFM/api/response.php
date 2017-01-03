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
	 * @var string
	 */
	private $username;

	/**
	 * @var int
	 */
	private $user_id;

	/**
	 * @var array
	 */
	private $errors = [];

	/**
	 * @var array
	 */
	private $listens = [];

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
		$this->user_id  = $user_id;
		$this->username = $username;
	}

	/**
	 * Returns an array containing the UserID as the first element, and the
	 * username as the second.
	 * @return string
	 */
	public function getUserID()
	{
		return $this->user_id;
	}

	/**
	 * @return string
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Returns the api_key of the client.
	 *
	 * @param string $client
	 */
	public function setClient($client)
	{
		$this->client = $client;
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
	public function add_listen($listen)
	{
		array_push($this->listens, $listen);
	}

	public function respond()
	{
		$response = json_encode(['VirunusFM' => $this->build_array()]);
		echo $response;
	}

	private function build_array()
	{
		$response              = [];
		$response['errors']    = $this->errors;
		$response['user_info'] = [
			'username' => $this->username,
			'client'   => $this->client
		];
		$response['method']    = $this->method;

		if ($this->method == 'write') {
			$y = 0;
			$n = 0;
			foreach ($this->listens as $val) {
				if (in_array(20, $val['status'], true) 
				    or in_array(24, $val['status'], true)
				) {
					$n ++;
				} else {
					$y ++;
				}
			}
			$response['success'] = $y;
			$response['failure'] = $n;
		} else if ($this->method == 'read') {
			$response['retrieved'] = count($this->listens);
		}
		$response['listens'] = $this->listens;

		return $response;
	}

}



<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 14:25
 * This is the main API file for VirunusFM.  Calls for the web service will
 * generate from here following an http request.
 */
require_once "../_config.php";
include "response.php";
use \Firebase\JWT\JWT;

header("Content-type: application/json");

$dbh = db_connect() or die(DB_CONNERR);
unset($auth_err);
$response = new Response();

$method = $_POST['method'];
$data   = $_POST['data'];

// Validate API and authenticate user.
$jwt = $data['token'];
try {
	$token = JWT::decode($jwt, JWT_KEY, ['HS256']);

	$response->setUser($token->user_id, $token->username);
	$response->setClient($token->client);

	//TODO: implement response handlers.
} catch (UnexpectedValueException $e) {
	echo json_encode($e->getMessage());
}

switch (strtolower($method)) {
	case "write":
		write($dbh, $response, $data['listens']);
		break;
	case "read":
		read($dbh, $response, $data['count']);
		break;
	default:
		$response->add_error(Errors::METHOD);
		break;
}

$response->setMethod($method);
$response->respond();

/**
 * @param mixed $row
 *
 * @return bool
 */
function check_client($row)
{
	if (is_a($row, "boolean")) {
		return false;
	} else if (is_null($row)) {
		return false;
	} else if ( !$row['active']) {
		return false;
	} else {
		return (strtotime($row['expires']) > time());
	}
}

/**
 * @param PDO $dbh
 * @param Response $response
 * @param array $listens
 */
function write($dbh, $response, $listens)
{
	foreach ($listens as $listen) {
		if (
			array_key_exists('artist', $listen)
			&& array_key_exists('track', $listen)
			&& array_key_exists('album', $listen)
			&& array_key_exists('datetime', $listen)
		) {
			$artist   = $listen['artist'];
			$track    = $listen['track'];
			$album    = $listen['album'];
			$datetime = $listen['datetime'];
			$status   = [];
			// Get artist ID.
			$stmt = $dbh->prepare("SELECT artist_id FROM artists WHERE name = ?");
			if ($stmt->execute([$artist])) {
				$artist_id = $stmt->fetch()[0];
			} else {
				$artist_id = 0;
				$artist    = "Unknown Artist";
				array_push($status, Errors::ARTIST_NOT_FOUND);
			}

			// Get album ID
			$stmt = $dbh->prepare("SELECT album_id FROM albums WHERE artist_id = ? AND title = ?");
			if ($stmt->execute([$artist_id, $album])) {
				$album_id = $stmt->fetch()[0];
			} else {
				$album_id = 0;
				$album    = "Unknown Album";
				array_push($status, Errors::ALBUM_NOT_FOUND);
			}

			// Get track ID
			$stmt = $dbh->prepare("SELECT track_id FROM tracks WHERE album_id = ? AND title = ?");
			if ($stmt->execute([$album_id, $track])) {
				$track_id = $stmt->fetch()[0];
			} else {
				// Add a new track
				$stmt = $dbh->prepare("INSERT INTO tracks(album_id, title) VALUES (?, ?)");
				$stmt->execute([$album_id, $track]);
				$track_id = $dbh->lastInsertId();
				array_push($status, Errors::TRACK_NOT_FOUND);
			}

			// Submit listen
			$stmt = $dbh->prepare("INSERT INTO listens(user_id, artist_id,album_id, track_id, api_key, datetime) VALUES (:user_id, :artist, :album, :track, :client, :datetime)");
			if ( !$stmt->execute([
				'user_id'  => $response->getUserID(),
				'artist'   => $artist_id,
				'album'    => $album_id,
				'track'    => $track_id,
				'client'   => $response->getClient(),
				'datetime' => $datetime
			])
			) {
				array_push($status, Errors::SUBMISSION_FAIL);
			}
		} else {
			$status = [Errors::NOT_ENOUGH_DATA];
			$artist = $album = $track = $datetime = null;
		}

		// Add response
		$response->add_listen([
			'status'   => $status,
			'artist'   => $artist,
			'album'    => $album,
			'track'    => $track,
			'datetime' => $datetime
		]);
	}
}

/**
 * @param PDO $dbh
 * @param Response $response
 * @param int $count
 */
function read($dbh, $response, $count)
{
	if ($count <= 50) {
		$stmt = $dbh->prepare("SELECT artist_id, album_id, track_id, api_key, datetime FROM listens WHERE user_id = :userid ORDER BY datetime DESC LIMIT :count");
		$stmt->bindParam('count', $count);
		$stmt->bindParam('userid', $response->getUserID());
		$stmt->execute();

		while ($row = $stmt->fetch()) {
			// Track
			$sql = $dbh->prepare("SELECT title FROM tracks WHERE track_id = :track AND album_id = :album");
			$sql->bindParam('track', $row['track_id']);
			$sql->bindParam('album', $row['album_id']);
			$sql->execute();
			$track = $sql->fetch()[0];

			// Album
			$sql = $dbh->prepare("SELECT title FROM albums WHERE album_id = :album AND artist_id = :artist");
			$sql->bindParam('artist', $row['artist_id']);
			$sql->bindParam('album', $row['album_id']);
			$sql->execute();
			$album = $sql->fetch()[0];

			// Artist
			$sql = $dbh->prepare("SELECT name FROM artists WHERE artist_id = :artist");
			$sql->bindParam('artist', $row['artist_id']);
			$sql->execute();
			$artist = $sql->fetch()[0];

			// Datetime
			$datetime = $row['datetime'];

			// Client
			$sql = $dbh->prepare("SELECT name FROM clients WHERE api_key = :api");
			$sql->execute([$row['api_key']]);
			$client = $sql->fetch()[0];

			// Add data to response
			$response->add_listen([
				'artist'   => $artist,
				'album'    => $album,
				'track'    => $track,
				'datetime' => $datetime,
				'client'   => $client
			]);
		}
	}


}

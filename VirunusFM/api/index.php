<?php
/**
 * User: Erik Wilson
 * Date: 30-Dec-16
 * Time: 14:25
 * This is the main API file for VirunusFM.  Calls for the web service will
 * generate from here following an http request.
 */
require_once "../_config.php";
include "read.php";
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
	$token = JWT::decode($jwt, 'secret', ['HS256']);

	$response->setUser($token->user_id, $token->username);
	$response->setClient($token->client);

	//TODO: implement response handlers.
} catch (\Firebase\JWT\BeforeValidException $e) {
	echo json_encode($e->getMessage());
} catch (\Firebase\JWT\ExpiredException $e) {
	echo json_encode($e->getMessage());
} catch (\Firebase\JWT\SignatureInvalidException $e) {
	echo json_encode($e->getMessage());
} catch (UnexpectedValueException $e) {
	echo json_encode($e->getMessage());
}

//switch (strtolower($method)) {
//	case "write":
//		write($dbh, $response, $data['listens']);
//		break;
//	case "read":
//		read(new Read());
//		break;
//	default:
//		$response->add_error(Errors::METHOD);
//		break;
//}

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
			list($artist, $track, $album, $datetime) = $listen;

			try {
				// Get artist ID.
				$stmt = $dbh->prepare("SELECT artist_id FROM artists WHERE name = ?");
				if ($stmt->execute([$artist])) {
					$artist_id = $stmt->fetch()[0];
				} else {
					$artist_id = 0;
					$artist = "Unknown Artist";
				}

				// Get album ID
				$stmt = $dbh->prepare("SELECT album_id FROM albums WHERE artist_id = $artist_id AND title = $album");
				if ($stmt->execute([$artist_id, $album])) {
					$album_id = $stmt->fetch()[0];
				} else {
					$album_id = 0;
					$album = "Unknown Album";
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
				}

				// Submit listen
				$stmt = $dbh->prepare("INSERT INTO listens(user_id, artist_id,album_id, track_id, api_key, datetime) VALUES (:user_id, :artist, :album, :track, :client, :datetime)");
				if ($stmt->execute([
					'user_id'  => $response->getUser()[0],
					'artist'   => $artist_id,
					'album'    => $album_id,
					'track'    => $track_id,
					'client'   => $response->getClient(),
					'datetime' => $datetime
				])
				) {
					$status = "success";
				} else {
					$status = "failure";
				}
			} catch (PDOException $e) {
				$status = "failure";
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
}

/**
 * @param Read $read
 */
function read($read)
{

}

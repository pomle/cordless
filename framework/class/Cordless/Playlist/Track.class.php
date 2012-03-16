<?
namespace Cordless\Playlist;

use \Asenine\DB;

class TrackException extends \Exception
{}

class Track
{
	protected
		$playlistTrackID,
		$timeAdded;

	public
		$playlistID,
		$adderUserID;

	public static function createInDB(self $PlaylistTrack)
	{
		$timeAdded = time();

		$query = DB::prepareQuery("INSERT INTO
			Cordless_PlaylistTracks (
				ID,
				playlistID,
				trackID,
				adder_userID,
				timeAdded
			) VALUES(
				NULL,
				%u,
				%u,
				NULLIF(%u, 0),
				%u
			)",
			$PlaylistTrack->playlistID,
			$PlaylistTrack->trackID,
			$PlaylistTrack->adderUserID,
			$timeAdded);

		if( $playlistTrackID = DB::queryAndGetID($query) )
		{
			$PlaylistTrack->playlistTrackID = $playlistTrackID;
			$PlaylistTrack->timeAdded = $timeAdded;
		}

		return true;
	}


	public static function loadFromDB($playlistTrackIDs)
	{
		if( !($returnArray = is_array($playlistTrackIDs)) )
			$playlistTrackIDs = array($playlistTrackIDs);

		$playlistTracks = array_fill_keys($playlistTrackIDs, false);

		$query = DB::prepareQuery("SELECT
				ID AS playlistTrackID,
				playlistID,
				trackID,
				adder_userID,
				timeAdded
			FROM
				Cordless_PlaylistTracks pt
			WHERE
				pt.ID IN %a",
			$playlistTrackIDs);

		$Result = DB::queryAndFetchResult($query);

		foreach($Result as $pt)
		{
			$PlaylistTrack = new self($pt['trackID'], $pt['adder_userID']);

			$PlaylistTrack->timeAdded = (int)$pt['timeAdded'] ?: null;
			$PlaylistTrack->playlistID = (int)$pt['playlistID'];

			$PlaylistTrack->playlistTrackID = (int)$pt['playlistTrackID'];

			$playlistTracks[] = $PlaylistTrack;
		}

		$playlistTracks = array_filter($playlistTracks);

		return $returnArray ? $playlistTracks : reset($playlistTracks);
	}

	public static function saveToDB($playlistTracks)
	{
		if( !is_array($playlistTracks) )
			$playlistTracks = array($playlistTracks);

		if( count($playlistTracks) )
		{
			$timeAdded = time();

			$track_Insert = "INSERT INTO Cordless_PlaylistTracks (
				ID,
				sortOrder
			) VALUES";

			$track_Value = "(
				%u,
				%u
			)";

			$track_Update = " ON DUPLICATE KEY UPDATE
				sortOrder = VALUES(sortOrder)";

			$track_Values = '';

			$iTrack = 0;
			foreach($playlistTracks as $index => $PlaylistTrack)
			{
				if( !$PlaylistTrack instanceof self )
					throw New TrackException(sprintf('PlaylistTrack array index %s must be instance of %s', $index, __CLASS__));

				if( !isset($PlaylistTrack->playlistTrackID) )
					self::createInDB($PlaylistTrack);

				$track_Values .= DB::prepareQuery($track_Value,
					$PlaylistTrack->playlistTrackID,
					$iTrack++
				) . ",";
			}

			$track_InsertQuery = $track_Insert . rtrim($track_Values, ',') . $track_Update;
			DB::query($track_InsertQuery);
		}

		return true;
	}


	public function __construct($trackID, $userID = 0)
	{
		$this->trackID = (int)$trackID;
		$this->adderUserID = (int)$userID ?: null;
	}

	public function __get($key)
	{
		return $this->$key;
	}


	public function getUserTrack(\CordlessUser $User)
	{
		return \Cordless\UserTrack::loadByTrack($User, $this->trackID);
	}
}
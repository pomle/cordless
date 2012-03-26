<?
namespace Cordless;

use \Asenine\DB;

class UserTrackException extends \Exception
{}

class UserTrack
{
	protected
		$userTrackID,
		$playcount,
		$Track,
		$Image;

	public
		$userID,
		$trackID,
		$filename,
		$artist,
		$title,
		$isStarred;


	public static function addStars(Array $userTrackIDs)
	{
		if( count($userTrackIDs) )
		{
			$timeNow = time();

			$query = "REPLACE INTO
				Cordless_UserTracksStarred (
					userTrackID,
					timeCreated
				) VALUES";

			foreach($userTrackIDs as $userTrackID)
				$query .= DB::prepareQuery("(%d, %d)", $userTrackID, $timeNow) . ",";

			DB::query(rtrim($query, ","));
		}

		return true;
	}


	public static function createInDB(self $UserTrack)
	{
		$timeCreated = time();

		if( !isset($UserTrack->Track) )
			throw new UserTrackException(sprintf("%s must have Track set", get_class($UserTrack)));

		$query = DB::prepareQuery("INSERT INTO
			Cordless_UserTracks
			(
				ID,
				userID,
				trackID,
				timeCreated
			) VALUES(
				NULL,
				%u,
				%u,
				NULLIF(%u, 0)
			)",
			$UserTrack->userID,
			$UserTrack->Track->trackID,
			$timeCreated);

		if( $userTrackID = DB::queryAndGetID($query) )
		{
			$UserTrack->userTrackID = $userTrackID;
			$UserTrack->timeCreated = $timeCreated;
		}

		return true;
	}

	public static function loadByTrack(User $User, $trackIDs)
	{
		if( $returnArray = is_array($trackIDs) )
			$trackIDs = (array)$trackIDs;

		$query = DB::prepareQuery("SELECT
				ut.ID
			FROM
				Cordless_Tracks t
				JOIN Cordless_UserTracks ut ON ut.trackID = t.ID
			WHERE
				t.ID IN %a
				AND ut.userID = %d",
			$trackIDs,
			$User->userID);

		$userTrackIDs = DB::queryAndFetchArray($query);

		return self::loadFromDB($returnArray ? $userTrackIDs : reset($userTrackIDs));
	}


	public static function loadFromDB($userTrackIDs)
	{
		if( !($returnArray = is_array($userTrackIDs)) )
			$userTrackIDs = array($userTrackIDs);

		$userTracks = array_fill_keys($userTrackIDs, false);

		$query = DB::prepareQuery("SELECT
				ut.ID AS userTrackID,
				ut.userID,
				ut.trackID,
				ut.image_mediaID,
				ut.timeCreated,
				ut.playcount,
				ut.filename,
				ut.artist,
				ut.title,
				uts.userTrackID AS isStarred
			FROM
				Cordless_UserTracks ut
				LEFT JOIN Cordless_UserTracksStarred uts ON uts.userTrackID = ut.ID
			WHERE
				ut.ID IN %a",
			$userTrackIDs);

		$Result = DB::queryAndFetchResult($query);

		$imageIDs = $trackIDs = array();

		foreach($Result as $userTrack)
		{
			$UserTrack = new static($userTrack['userID']);

			$UserTrack->userTrackID = (int)$userTrack['userTrackID'];
			$UserTrack->userID = (int)$userTrack['userID'];
			$UserTrack->trackID = (int)$userTrack['trackID'];
			$UserTrack->imageID = (int)$userTrack['image_mediaID'] ?: null;

			$UserTrack->timeCreated = (int)$userTrack['timeCreated'] ?: null;
			$UserTrack->playcount = (int)$userTrack['playcount'];

			$UserTrack->filename = $userTrack['filename'];
			$UserTrack->artist = $userTrack['artist'];
			$UserTrack->title = $userTrack['title'];

			$UserTrack->isStarred = (bool)$userTrack['isStarred'];

			$userTracks[$UserTrack->userTrackID] = $UserTrack;

			if( $UserTrack->imageID )
				$imageIDs[] = $UserTrack->imageID;

			$trackIDs[] = $UserTrack->trackID;
		}

		$userTracks = array_filter($userTracks);

		if( count($imageIDs) )
		{
			$images = \Asenine\Media::loadFromDB($imageIDs);
			foreach($userTracks as $UserTrack)
				if( isset($images[$UserTrack->imageID]) )
					$UserTrack->setImage($images[$UserTrack->imageID]);

			unset($images);
		}


		$tracks = Track::loadFromDB($trackIDs);
		foreach($userTracks as $UserTrack)
		{
			if( isset($tracks[$UserTrack->trackID]) )
			{
				$Track = $tracks[$UserTrack->trackID];

				$UserTrack->setTrack($Track);

				if( !isset($UserTrack->Image) )
				{
					if( isset($Track->Image) )
						$UserTrack->setImage($Track->Image);

					elseif( isset($Track->artists[0]->Image) )
						$UserTrack->setImage($Track->artists[0]->Image);
				}

				if( !isset($UserTrack->artist) )
					$UserTrack->artist = $Track->getArtist();

				if( !isset($UserTrack->title) )
					$UserTrack->title = (string)$Track;
			}
		}

		return $returnArray ? $userTracks : reset($userTracks);
	}

	public static function registerPlays($userTrackIDs, $uts, $duration = 0)
	{
		$userTrackIDs = (array)$userTrackIDs;

		if( count($userTrackIDs) > 0 )
		{
			$query = "INSERT INTO Cordless_UserTrackPlays (userTrackID, timeCreated, duration) VALUES";
			foreach($userTrackIDs as $userTrackID)
				$query .= DB::prepareQuery("(%d, %d, %d),", $userTrackID, $uts, $duration);

			$query = rtrim($query, ',');
			DB::query($query);


			$query = DB::prepareQuery("UPDATE
					Cordless_UserTracks
				SET
					timeLastPlayed = %d,
					playCount = playCount + %d
				WHERE
					ID IN %a",
				$uts,
				1,
				$userTrackIDs);

			DB::query($query);
		}

		return true;
	}

	public static function removeFromDB(self $UserTrack)
	{
		$userTrackID = $UserTrack->userTrackID;

		$query = DB::prepareQuery("DELETE FROM Cordless_UserTracks WHERE ID = %d", $userTrackID);
		DB::query($query);

		return true;
	}

	public static function removeStars(Array $userTrackIDs)
	{
		if( count($userTrackIDs) )
		{
			$query = DB::prepareQuery("DELETE FROM Cordless_UserTracksStarred WHERE userTrackID IN %a", $userTrackIDs);
			DB::query($query);
		}

		return true;
	}

	public static function saveToDB($userTracks)
	{
		if( !is_array($userTracks) )
			$userTracks = array($userTracks);


		$timeModified = time();

		$userTrack_Insert = "INSERT INTO Cordless_UserTracks (
			ID,
			image_mediaID,
			filename,
			artist,
			title
		) VALUES";

		$userTrack_Value = "(
			%u,
			NULLIF(%d, 0),
			NULLIF(%s, ''),
			NULLIF(%s, ''),
			NULLIF(%s, '')
		)";

		$userTrack_Update = " ON DUPLICATE KEY UPDATE
			image_mediaID = VALUES(image_mediaID),
			filename = VALUES(filename),
			artist = VALUES(artist),
			title = VALUES(title)";


		$userTrack_Values = '';

		foreach($userTracks as $UserTrack)
		{
			if( !isset($UserTrack->userTrackID) )
				self::createInDB($UserTrack);

			$userTrack_Values .= DB::prepareQuery($userTrack_Value,
				$UserTrack->userTrackID,
				isset($UserTrack->Image) ? $UserTrack->Image->mediaID : 0,
				$UserTrack->filename,
				$UserTrack->artist,
				$UserTrack->title) . ",";
		}

		$userTrack_InsertQuery = $userTrack_Insert . rtrim($userTrack_Values, ',') . $userTrack_Update;
		DB::query($userTrack_InsertQuery);

		return true;
	}


	public function __construct($userID)
	{
		$this->userID = $userID;
		$this->playcount = 0;
	}

	public function __get($key)
	{
		return $this->$key;
	}

	public function __isset($key)
	{
		return isset($this->$key);
	}

	public function __toString()
	{
		return sprintf('%s - %s', $this->artist, $this->title);
	}


	public function getCaption()
	{
		return sprintf('%s - %s', $this->artist, $this->title);
	}

	public function isAccessible(User $User)
	{
		$query = DB::prepareQuery("SELECT
				( COUNT(*) > 0 ) AS isAccessible
			FROM
				Cordless_UserTracks ut
				LEFT JOIN Cordless_UserFriends uf ON uf.userID = ut.userID
			WHERE
				ut.ID = %d
				AND
				(
					ut.userID = %d
					OR uf.friendUserID = %d
				)",
			$this->userTrackID,
			$User->userID,
			$User->userID);

		return (bool)DB::queryAndFetchOne($query);
	}

	public function isOwner(User $User)
	{
		return ( $this->userID == $User->userID );
	}

	public function registerPlay($duration = 0)
	{
		return ( self::registerPlays($this->userTrackID, time(), $duration) );
	}

	public function setImage(\Asenine\Media\Type\Image $Image)
	{
		$this->Image = $Image;
		return $this;
	}

	public function setTrack(Track $Track)
	{
		$this->Track = $Track;
		return $this;
	}

	public function star()
	{
		self::addStars(array($this->userTrackID));
		$this->isStarred = true;
		return true;
	}

	public function takeOwnership($userID)
	{
		$this->userTrackID = null;
		$this->userID = (int)$userID;
		return true;
	}

	public function unstar()
	{
		self::removeStars(array($this->userTrackID));
		$this->isStarred = false;
		return true;
	}
}
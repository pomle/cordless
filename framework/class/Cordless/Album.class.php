<?
namespace Cordless;

use \Asenine\DB;

class AlbumException extends \Exception
{}

class Album
{
	protected
		$albumID,
		$Image,
		$timeCreated,
		$tracks;

	public
		$title,
		$timeReleased;


	public static function createInDB(self $Album)
	{
		$timeCreated = time();

		$query = DB::prepareQuery("INSERT INTO
			Cordless_Albums
			(
				ID,
				timeCreated,
				timeReleased,
				title
			) VALUES(
				NULL,
				NULLIF(%u, 0),
				NULLIF(%u, 0),
				NULLIF(%s, '')
			)",
			$timeCreated,
			$Album->timeReleased,
			$Album->title);

		if( $albumID = DB::queryAndGetID($query) )
		{
			$Album->albumID = $albumID;
			$Album->timeCreated = $timeCreated;
		}

		return true;
	}


	public static function loadByTitle($title)
	{
		$query = DB::prepareQuery("SELECT ID FROM Cordless_Albums WHERE title = %s", $title);
		$albumID = DB::queryAndFetchOne($query);
		return self::loadFromDB($albumID);
	}

	public static function loadFromDB($albumIDs, $skipTracks = false)
	{
		if( !($returnArray = is_array($albumIDs)) )
			$albumIDs = array($albumIDs);

		$albums = array_fill_keys($albumIDs, false);

		$query = DB::prepareQuery("SELECT
				a.ID AS albumID,
				a.image_mediaID,
				a.timeCreated,
				a.timeReleased,
				a.title
			FROM
				Cordless_Albums a
			WHERE
				a.ID IN %a", $albumIDs);

		$Result = DB::queryAndFetchResult($query);

		$image_mediaIDs = array();

		foreach($Result as $album)
		{
			$Album = new self($album['title']);

			$Album->albumID = (int)$album['albumID'];
			$Album->image_mediaID = $album['image_mediaID'];

			$Album->timeCreated = (int)$album['timeCreated'] ?: null;
			$Album->timeReleased = (int)$album['timeReleased'] ?: null;

			$albums[$Album->albumID] = $Album;

			if( $Album->image_mediaID )
				$image_mediaIDs[] = $Album->image_mediaID;
		}

		$albums = array_filter($albums);
		$albumIDs = array_keys($albums);

		$images = \Asenine\Media::loadFromDB($image_mediaIDs);
		foreach($albums as $Album)
		{
			if( isset($images[$Album->image_mediaID]) ) $Album->setImage($images[$Album->image_mediaID]);
			unset($Album->image_mediaID);
		}
		unset($images);


		if( $skipTracks !== true )
		{
			$query = DB::prepareQuery("SELECT
					at.albumID,
					at.trackID,
					at.trackNo
				FROM
					Cordless_AlbumTracks at
				WHERE
					at.albumID IN %a",
				$albumIDs);

			$Result = DB::queryAndFetchResult($query);

			$albumIDs = array();
			$trackIDs = array();

			foreach($Result as $row)
			{
				$albumID = (int)$row['albumID'];
				$trackID = (int)$row['trackID'];

				$trackIDs[] = $trackID;
				$albumIDs[$albumID][$trackID] = $row['trackNo'];
			}


			$tracks = Track::loadFromDB($trackIDs);

			foreach($albumIDs as $albumID => $trackIDs)
			{
				if( !isset($albums[$albumID]) ) continue;

				$Album = $albums[$albumID];

				foreach($trackIDs as $trackID => $trackNo)
				{
					if( !isset($tracks[$trackID]) ) continue;

					$Track = $tracks[$trackID];
					$Track->trackNo = $trackNo;
					$Album->addTrack($Track);
				}
			}
		}

		return $returnArray ? $albums : reset($albums);
	}

	public static function saveToDB($albums)
	{
		if( !is_array($albums) )
			$albums = array($albums);

		$timeModified = time();

		$album_Insert = "INSERT INTO Cordless_Albums (
			ID,
			image_mediaID,
			timeReleased,
			title
		) VALUES";

		$album_Value = "(
			%u,
			NULLIF(%u, 0),
			NULLIF(%u, 0),
			NULLIF(%s, '')
		)";

		$album_Update = " ON DUPLICATE KEY UPDATE
			image_mediaID = VALUES(image_mediaID),
			timeReleased = VALUES(timeReleased),
			title = VALUES(title)";

		$album_Values = '';


		$albumTracks_Insert = "INSERT INTO Cordless_AlbumTracks (
			albumID,
			trackID,
			trackNo
		) VALUES";

		$albumTracks_Value = "(
			%u,
			%u,
			NULLIF(%u, 0)
		)";

		$albumTracks_Values = '';
		$albumTracks_DeleteIDs = array();


		foreach($albums as $index => $Album)
		{
			if( !$Album instanceof self )
				throw New TrackException(sprintf('Album array index %s must be instance of %s', $index, __CLASS__));

			if( !isset($Album->albumID) )
				self::createInDB($Album);

			$album_Values .= DB::prepareQuery($album_Value,
				$Album->albumID,
				isset($Album->Image) ? $Album->Image->mediaID : 0,
				$Album->timeReleased,
				$Album->title) . ",";


			$albumTracks_DeleteIDs[] = $Album->albumID;

			foreach($Album->tracks as $Track)
			{
				$albumTracks_Values .= DB::prepareQuery($albumTracks_Value,
					$Album->albumID,
					$Track->trackID,
					$Track->trackNo) . ",";
			}
		}

		$album_InsertQuery = $album_Insert . rtrim($album_Values, ',') . $album_Update;
		DB::query($album_InsertQuery);

		$albumTracks_DeleteQuery = DB::prepareQuery("DELETE FROM Cordless_AlbumTracks WHERE albumID IN %a", $albumTracks_DeleteIDs);
		DB::query($albumTracks_DeleteQuery);

		$albumTracks_InsertQuery = $albumTracks_Insert . rtrim($albumTracks_Values, ',');
		DB::query($albumTracks_InsertQuery);

		return true;
	}


	public function __construct($title, $timeReleased = null)
	{
		$this->title = $title;
		$this->timeReleased = $timeReleased;
		$this->tracks = array();
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
		return $this->title;
	}

	public function addTrack(Track $Track)
	{
		$this->tracks[] = $Track;
		return $this;
	}

	public function dropTrack($index)
	{
		if( isset($this->tracks[$index]) )
		{
			unset($this->tracks[$index]);
			return true;
		}

		return false;
	}

	public function setImage(\Media\Image $Image)
	{
		$this->Image = $Image;
		return $this;
	}
}
<?
namespace Cordless;

use \Asenine\DB;

class TrackException extends \Exception
{}

class Track
{
	protected
		$trackID,
		$Audio,
		$Image,
		$timeCreated;

	public
		$timeReleased,
		$title,
		$artists,
		$duration;


	public static function createInDB(self $Track)
	{
		if( !isset($Track->Audio) || !isset($Track->Audio->mediaID) )
			throw New TrackException("Track Audio mediaID must be set before creating database post");

		$timeCreated = time();

		$query = DB::prepareQuery("INSERT INTO
			Cordless_Tracks
			(
				ID,
				audio_mediaID,
				timeCreated,
				title
			) VALUES(
				NULL,
				NULLIF(%u, 0),
				NULLIF(%u, 0),
				NULLIF(%s, '')
			)",
			$Track->Audio->mediaID,
			$timeCreated,
			$Track->title);

		if( $trackID = DB::queryAndGetID($query) )
		{
			$Track->trackID = $trackID;
			$Track->timeCreated = $timeCreated;
		}

		return true;
	}

	public static function loadByTrack(self $Track)
	{
		$artistIDs = array();
		foreach($Track->artists as $Artist)
			$artistIDs[] = $Artist->artistID;

		$query = DB::prepareQuery("SELECT
				t.ID
			FROM
				Cordless_Tracks t
				JOIN Cordless_TrackArtists ta ON ta.trackID = t.ID
				JOIN Asenine_Media m ON m.ID = t.audio_mediaID
			WHERE
				m.fileHash = %s
				AND t.title = %s
				AND ta.artistID IN %a
			GROUP BY
				t.ID
			HAVING
				COUNT(*) = %d",
			$Track->Audio->mediaHash,
			$Track->title,
			$artistIDs,
			count($artistIDs));

		$trackIDs = DB::queryAndFetchArray($query);

		if( count($trackIDs) == 1 )
			return self::loadFromDB(reset($trackIDs));
		elseif(  count($trackIDs) > 1 ) ### Will-be case for several track
			throw New TrackException(__METHOD__ . ': Several matching tracks found');
		else
			return false;
	}

	public static function loadFromDB($trackIDs)
	{
		if( !($returnArray = is_array($trackIDs)) )
			$trackIDs = array($trackIDs);

		$tracks = array_fill_keys($trackIDs, false);

		$query = DB::prepareQuery("SELECT
				t.ID AS trackID,
				t.audio_mediaID,
				t.image_mediaID,
				t.timeCreated,
				t.timeReleased,
				t.title,
				t.duration
			FROM
				Cordless_Tracks t
			WHERE
				t.ID IN %a", $trackIDs);

		$Result = DB::queryAndFetchResult($query);

		$image_mediaIDs = $audio_mediaIDs = array();

		foreach($Result as $track)
		{
			$Track = new self($track['title']);

			$Track->trackID = (int)$track['trackID'];

			$Track->audio_mediaID = $track['audio_mediaID'];
			$Track->image_mediaID = $track['image_mediaID'];

			$Track->timeCreated = (int)$track['timeCreated'] ?: null;
			$Track->timeReleased = (int)$track['timeReleased'] ?: null;

			$Track->duration = (int)$track['duration'] ?: null;

			$Track->artistIDs = array();

			$tracks[$Track->trackID] = $Track;

			if( $Track->audio_mediaID )
				$audio_mediaIDs[] = $Track->audio_mediaID;

			if( $Track->image_mediaID )
				$image_mediaIDs[] = $Track->image_mediaID;
		}

		$tracks = array_filter($tracks);


		$audios = \Asenine\Media::loadFromDB($audio_mediaIDs);

		foreach($tracks as $Track)
			if( isset($audios[$Track->audio_mediaID]) ) $Track->setAudio($audios[$Track->audio_mediaID]);

		unset($audios);


		$images = \Asenine\Media::loadFromDB($image_mediaIDs);

		foreach($tracks as $Track)
			if( isset($images[$Track->image_mediaID]) ) $Track->setImage($images[$Track->image_mediaID]);

		unset($images);


		$query = DB::prepareQuery("SELECT trackID, artistID FROM Cordless_TrackArtists WHERE trackID IN %a", $trackIDs);
		$Result = DB::queryAndFetchResult($query);

		$artistIDs = array();

		foreach($Result as $trackArtists)
		{
			$trackID = (int)$trackArtists['trackID'];
			$artistID = (int)$trackArtists['artistID'];

			if( isset($tracks[$trackID]) )
			{
				$Track = $tracks[$trackID];

				$Track->artistIDs[] = $artistID;
				$artistIDs[] = $artistID;
			}
		}

		$artists = Artist::loadFromDB($artistIDs);

		foreach($tracks as $Track)
		{
			foreach($Track->artistIDs as $artistID)
				if( isset($artists[$artistID]) ) $Track->addArtist($artists[$artistID]);

			unset($Track->artistIDs);
		}

		return $returnArray ? $tracks : reset($tracks);
	}

	public static function saveToDB($tracks)
	{
		if( !is_array($tracks) )
			$tracks = array($tracks);


		$timeModified = time();

		$track_Insert = "INSERT INTO Cordless_Tracks (
			ID,
			audio_mediaID,
			image_mediaID,
			timeReleased,
			title,
			duration
		) VALUES";

		$track_Value = "(
			%u,
			%u,
			NULLIF(%u, 0),
			NULLIF(%u, 0),
			NULLIF(%s, ''),
			NULLIF(%d, 0)
		)";

		$track_Update = " ON DUPLICATE KEY UPDATE
			audio_mediaID = VALUES(audio_mediaID),
			image_mediaID = VALUES(image_mediaID),
			timeReleased = VALUES(timeReleased),
			title = VALUES(title),
			duration = VALUES(duration)";

		$trackArtists_Insert = "INSERT INTO Cordless_TrackArtists (
			trackID,
			artistID
		) VALUES";

		$trackArtists_Value = "(
			%u,
			%u
		)";

		$track_Values = '';
		$trackArtists_Values = '';
		$trackArtists_DeleteIDs = array();

		foreach($tracks as $index => $Track)
		{
			if( !$Track instanceof self )
				throw New TrackException(sprintf('Track array index %s must be instance of %s', $index, __CLASS__));

			if( !isset($Track->trackID) )
				self::createInDB($Track);

			$track_Values .= DB::prepareQuery($track_Value,
				$Track->trackID,
				$Track->Audio->mediaID,
				isset($Track->Image) ? $Track->Image->mediaID : 0,
				$Track->timeReleased,
				$Track->title,
				max($Track->duration, (int)$Track->duration)
			) . ",";

			$trackArtists_DeleteIDs[] = $Track->trackID;

			foreach($Track->artists as $Artist)
			{
				$trackArtists_Values .= DB::prepareQuery($trackArtists_Value,
					$Track->trackID,
					$Artist->artistID) . ",";
			}
		}

		$track_InsertQuery = $track_Insert . rtrim($track_Values, ',') . $track_Update;
		DB::query($track_InsertQuery);

		$trackArtists_DeleteQuery = DB::prepareQuery("DELETE FROM Cordless_TrackArtists WHERE trackID IN %a", $trackArtists_DeleteIDs);
		DB::query($trackArtists_DeleteQuery);

		$trackArtists_InsertQuery = $trackArtists_Insert . rtrim($trackArtists_Values, ',');
		DB::query($trackArtists_InsertQuery);

		return true;
	}


	public function __construct($title)
	{
		$this->title = $title;
		$this->artists = array();
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

	public function addArtist(Artist $Artist)
	{
		$this->artists[] = $Artist;
		return $this;
	}

	public function getArtist()
	{
		return join(", ", $this->artists);
	}

	public function getName()
	{
		return sprintf('%s - %s', $this->getArtist(), $this->title);
	}

	public function setAudio(\Asenine\Media\Type\Audio $Audio)
	{
		$this->Audio = $Audio;
		return $this;
	}

	public function setImage(\Asenine\Media\Type\Image $Image)
	{
		$this->Image = $Image;
		return $this;
	}
}
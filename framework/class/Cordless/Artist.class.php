<?
namespace Cordless;

use \Asenine\DB;

class ArtistException extends \Exception
{}

class Artist
{
	protected
		$artistID,
		$Image,
		$timeCreated;

	public
		$name;


	public static function createInDB(self $Artist)
	{
		$timeCreated = time();

		$query = DB::prepareQuery("INSERT INTO
			Cordless_Artists
			(
				ID,
				timeCreated,
				name
			) VALUES(
				NULL,
				NULLIF(%u, 0),
				NULLIF(%s, '')
			)",
			$timeCreated,
			$Artist->name);

		if( $artistID = DB::queryAndGetID($query) )
		{
			$Artist->artistID = $artistID;
			$Artist->timeCreated = $timeCreated;
		}

		return true;
	}


	public static function loadByName($name)
	{
		return self::loadFromDB( self::getIDsByName($name) );
	}

	public static function loadFromDB($artistIDs)
	{
		if( !($returnArray = is_array($artistIDs)) )
			$artistIDs = array($artistIDs);

		$artists = array_fill_keys($artistIDs, false);

		$query = DB::prepareQuery("SELECT
				a.ID AS artistID,
				a.image_mediaID,
				a.timeCreated,
				a.name
			FROM
				Cordless_Artists a
			WHERE
				a.ID IN %a", $artistIDs);

		$Result = DB::queryAndFetchResult($query);

		$image_mediaIDs = array();

		foreach($Result as $artist)
		{
			$Artist = new self($artist['name']);

			$Artist->artistID = (int)$artist['artistID'];
			$Artist->image_mediaID = $artist['image_mediaID'];

			$Artist->timeCreated = (int)$artist['timeCreated'] ?: null;

			$artists[$Artist->artistID] = $Artist;

			if( $Artist->image_mediaID )
				$image_mediaIDs[] = $Artist->image_mediaID;
		}

		$artists = array_filter($artists);

		$images = \Asenine\Media::loadFromDB($image_mediaIDs);

		foreach($artists as $Artist)
			if( isset($images[$Artist->image_mediaID]) ) $Artist->setImage($images[$Artist->image_mediaID]);

		unset($images);

		return $returnArray ? $artists : reset($artists);
	}

	public static function getIDsByName($name)
	{
		$query = DB::prepareQuery("SELECT ID FROM Cordless_Artists WHERE name = %s", $name);
		$artistIDs = DB::queryAndFetchArray($query);
		return $artistIDs;
	}

	public static function saveToDB($artists)
	{
		if( !is_array($artists) )
			$artists = array($artists);


		$timeModified = time();

		$artist_Insert = "INSERT INTO Cordless_Artists (
			ID,
			image_mediaID,
			name
		) VALUES";

		$artist_Value = "(
			%u,
			NULLIF(%u, 0),
			NULLIF(%s, '')
		)";

		$artist_Update = " ON DUPLICATE KEY UPDATE
			image_mediaID = VALUES(image_mediaID),
			name = VALUES(name)";

		$artist_Values = '';

		foreach($artists as $index => $Artist)
		{
			if( !$Artist instanceof self )
				throw New TrackException(sprintf('Artist array index %s must be instance of %s', $index, __CLASS__));

			if( !isset($Artist->artistID) )
				self::createInDB($Artist);

			$artist_Values .= DB::prepareQuery($artist_Value,
				$Artist->artistID,
				isset($Artist->Image) ? $Artist->Image->mediaID : 0,
				$Artist->name) . ",";
		}

		$artist_InsertQuery = $artist_Insert . rtrim($artist_Values, ',') . $artist_Update;
		DB::query($artist_InsertQuery);

		return true;
	}


	public function __construct($name)
	{
		$this->name = $name;
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
		return $this->name;
	}

	public function setImage(\Asenine\Media\Type\Image $Image)
	{
		$this->Image = $Image;
		return $this;
	}
}
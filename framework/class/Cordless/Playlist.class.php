<?
namespace Cordless;

use \Asenine\DB;

class PlaylistException extends \Exception
{}

class Playlist
{
	protected
		$playlistID,
		$Image,
		$timeCreated,
		$tracks = array();

	public
		$title;


	public static function createInDB(self $Playlist)
	{
		$timeCreated = time();

		$query = DB::prepareQuery("INSERT INTO
			Cordless_Playlists
			(
				ID,
				timeCreated,
				title
			) VALUES(
				NULL,
				NULLIF(%u, 0),
				NULLIF(%s, '')
			)",
			$timeCreated,
			$Playlist->title);

		if( $playlistID = DB::queryAndGetID($query) )
		{
			$Playlist->playlistID = $playlistID;
			$Playlist->timeCreated = $timeCreated;
		}

		return true;
	}

	public static function loadFromDB($playlistIDs, $skipTracks = false)
	{
		if( !($returnArray = is_array($playlistIDs)) )
			$playlistIDs = array($playlistIDs);

		$playlists = array_fill_keys($playlistIDs, false);

		$query = DB::prepareQuery("SELECT
				p.ID AS playlistID,
				p.image_mediaID,
				p.timeCreated,
				p.title
			FROM
				Cordless_Playlists p
			WHERE
				p.ID IN %a",
			$playlistIDs);

		$Result = DB::queryAndFetchResult($query);

		$image_mediaIDs = array();

		foreach($Result as $playlist)
		{
			$Playlist = new self();

			$Playlist->title = $playlist['title'];

			$Playlist->playlistID = (int)$playlist['playlistID'];
			$Playlist->image_mediaID = $playlist['image_mediaID'];

			$Playlist->timeCreated = (int)$playlist['timeCreated'] ?: null;

			$playlists[$Playlist->playlistID] = $Playlist;

			if( $Playlist->image_mediaID )
				$image_mediaIDs[] = $Playlist->image_mediaID;
		}

		$playlists = array_filter($playlists);
		$playlistIDs = array_keys($playlists);

		if( count($image_mediaIDs) )
		{
			$images = \Asenine\Media::loadFromDB($image_mediaIDs);
			foreach($playlists as $Playlist)
			{
				if( isset($images[$Playlist->image_mediaID]) ) $Playlist->setImage($images[$Playlist->image_mediaID]);
				unset($Playlist->image_mediaID);
			}
			unset($images);
		}


		if( $skipTracks !== true )
		{
			$query = DB::prepareQuery("SELECT ID FROM Cordless_PlaylistTracks WHERE playlistID IN %a ORDER BY sortOrder ASC", $playlistIDs);
			$playlistTrackIDs = DB::queryAndFetchArray($query);

			$playlistTracks = Playlist\Track::loadFromDB($playlistTrackIDs);

			foreach($playlistTracks as $PlaylistTrack)
				if( isset($playlists[$PlaylistTrack->playlistID]) )
					$playlists[$PlaylistTrack->playlistID]->tracks[] = $PlaylistTrack;
		}

		return $returnArray ? $playlists : reset($playlists);
	}

	public static function removeFromDB(self $Playlist)
	{
		$playlistID = $Playlist->playlistID;

		$query = DB::prepareQuery("DELETE FROM Cordless_Playlists WHERE ID = %d", $playlistID);
		DB::query($query);

		return true;
	}

	public static function saveToDB($playlists)
	{
		if( !is_array($playlists) )
			$playlists = array($playlists);

		$timeModified = time();

		$playlist_Insert = "INSERT INTO Cordless_Playlists (
			ID,
			image_mediaID,
			title
		) VALUES";

		$playlist_Value = "(
			%u,
			NULLIF(%u, 0),
			NULLIF(%s, '')
		)";

		$playlist_Update = " ON DUPLICATE KEY UPDATE
			image_mediaID = VALUES(image_mediaID),
			title = VALUES(title)";

		$playlist_Values = '';


		$keepPlaylistIDs = $keepPlaylistTrackIDs = array();
		$tracks = array();

		foreach($playlists as $index => $Playlist)
		{
			if( !$Playlist instanceof self )
				throw New TrackException(sprintf('Playlist array index %s must be instance of %s', $index, __CLASS__));

			if( !isset($Playlist->playlistID) )
				self::createInDB($Playlist);

			$playlist_Values .= DB::prepareQuery($playlist_Value,
				$Playlist->playlistID,
				isset($Playlist->Image) ? $Playlist->Image->mediaID : 0,
				$Playlist->title
			) . ",";


			foreach($Playlist->tracks as $PlaylistTrack)
			{
				$PlaylistTrack->playlistID = $Playlist->playlistID;
				$tracks[] = $PlaylistTrack;
			}

			$keepPlaylistIDs[] = $Playlist->playlistID;
		}

		$playlist_InsertQuery = $playlist_Insert . rtrim($playlist_Values, ',') . $playlist_Update;
		DB::query($playlist_InsertQuery);

		if( count($keepPlaylistIDs) )
		{
			$track_DeleteQuery = DB::prepareQuery("DELETE FROM Cordless_PlaylistTracks WHERE playlistID IN %a", $keepPlaylistIDs, $keepPlaylistTrackIDs);
			#echo $track_DeleteQuery;
			DB::query($track_DeleteQuery);
		}

		Playlist\Track::saveToDB($tracks);

		/*
		foreach($tracks as $PlaylistTrack)
			$keepPlaylistTrackIDs[] = $PlaylistTrack->playlistTrackID;

		if( count($keepPlaylistIDs) && count($keepPlaylistTrackIDs) )
		{
			$track_DeleteQuery = DB::prepareQuery("DELETE FROM Cordless_PlaylistTracks WHERE playlistID IN %a AND NOT ID IN %a", $keepPlaylistIDs, $keepPlaylistTrackIDs);
			echo $track_DeleteQuery;
			DB::query($track_DeleteQuery);
		}*/

		return true;
	}


	public function __construct()
	{
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
		$this->tracks[] = new Playlist\Track($Track->trackID);
		return $this;
	}

	public function addUserTrack(UserTrack $UserTrack)
	{
		$this->tracks[] = new Playlist\Track($UserTrack->Track->trackID, $UserTrack->userID);
		return $this;
	}

	public function assignCreator(User $User)
	{
		$query = DB::prepareQuery("INSERT INTO
			Cordless_UserPlaylists (
				playlistID,
				userID,
				isCreator
			) VALUES(
				%d,
				%d,
				%d
			) ON DUPLICATE KEY UPDATE
				isCreator = VALUES(isCreator)",
			$this->playlistID,
			$User->userID,
			1);

		DB::query($query);

		return true;
	}

	public function clearTracks()
	{
		$this->tracks = array();
		return $this;
	}

	public function setImage(\Media\Image $Image)
	{
		$this->Image = $Image;
		return $this;
	}
}
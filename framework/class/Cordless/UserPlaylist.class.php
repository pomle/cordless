<?
namespace Cordless;

use \Asenine\DB;

class UserPlaylistException extends \Exception
{}

class UserPlaylist
{
	protected
		$Playlist new Playlist(),
		$isCreator = false,
		$isEditor = false;

	public
		$isSubscriber = false;


	public static function createInDB(self $UserPlaylist)
	{
		if( !isset($UserPlaylist->Playlist) )
			throw new UserTrackException(sprintf("%s must have Track set", get_class($UserTrack)));

		if( !isset($UserPlaylist->Playlist->playlistID) )
		{
			$isCreator = true;
			$isEditor = true;
			$isSubscriber = true;
			Playlist::saveToDB($UserPlaylist->Playlist);
		}

		$query = DB::prepareQuery("INSERT INTO
			Cordless_UserPlaylist
			(
				playlistID,
				userID,
				isCreator,
				isEditor,
				isSubscriber
			) VALUES(
				%u,
				%u,
				%u,
				%u
				%u
			)",
			$UserPlaylist->Playlist->playlistID,
			$UserPlaylist->userID,
			$isCreator,
			$isEditor,
			$isSubscriber);

		if( $userPlaylistID = DB::queryAndGetID($query) )
		{
			$UserPlaylist->userPlaylistID = $userPlaylistID;
			$UserPlaylist->isCreator = $isCreator;
			$UserPlaylist->isEditor = $isEditor;
			$UserPlaylist->isSubscriber = $isSubscriber;
		}

		return true;
	}

	public static function loadFromDB($playlistIDs, $userID)
	{
		if( !($returnArray = is_array($playlistIDs)) )
			$playlistIDs = array($playlistIDs);

		$playlists = Playlist::loadFromDB($playlistIDs);
		$playlistIDs = array_keys($playlists);

		$query = DB::prepareQuery("SELECT
				up.playlistID,
				up.isCreator,
				up.isEditor,
				up.isSubscriber
			FROM
				Cordless_UserPlaylists up
			WHERE
				up.ID IN %a",
			$playlistIDs);

		$result = DB::queryAndFetchResult($query);

		$map = array();
		while($userPlaylist = DB::assoc($result))
			$map[(int)$userPlaylist['playlistID']] = $userPlaylist;


		$userPlaylists = array();

		foreach($playlists as $Playlist)
		{
			$playlistID = $Playlist->playlistID;

			$UserPlaylist = new self($Playlist);
			$UserPlaylist->isCreator = (bool)$map[$playlistID]['isCreator'];
			$UserPlaylist->isEditor = (bool)$map[$playlistID]['isCreator'];
			$UserPlaylist->isSubscriber = (bool)$map[$playlistID]['isCreator'];

			$userPlaylists[$Playlist->playlistID] = $UserPlaylist;
		}

		$UserPlaylist = reset($userPlaylists); ### Resets array pointer and prepares for single value return

		return $returnArray ? $userPlaylists : $UserPlaylist;
	}


	public function __construct(Playlist $Playlist)
	{
		$this->Playlist = $Playlist;
	}

	public function __get($key)
	{
		return $this->$key;
	}

	public function __isset($key)
	{
		return isset($this->$key);
	}
}
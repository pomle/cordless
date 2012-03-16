<?
namespace Cordless;

use \Asenine\DB;

try
{
	if( !isset($_GET['playlistID']) || !$Playlist = \Music\Playlist::loadFromDB($_GET['playlistID']) )
		throw New \Exception('Invalid Playlist');

	echo Element\Library::head($Playlist->title);

	$Fetch = new Fetch\UserTrack($User, 'byPlaylist', $Playlist->playlistID);

	$userTracks = $Fetch();

	if( count($userTracks) == 0 )
		throw New \Exception(sprintf(_("Playlist seems empty"), htmlspecialchars($artist)));

	echo Element\Tracklist::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo Element\Message::error($e->getMessage());
}
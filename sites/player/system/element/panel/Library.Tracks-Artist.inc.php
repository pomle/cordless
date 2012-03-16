<?
namespace Cordless;

use \Asenine\DB;

try
{
	if( !isset($_GET['artist']) )
		throw New \Exception('No artist name given');

	$artist = $_GET['artist'];

	echo Element\Library::head($artist);

	$Fetch = new Fetch\UserTrack($User, 'byArtist', $artist);

	$userTracks = $Fetch();

	if( count($userTracks) == 0 )
		throw New \Exception(sprintf(_("No matches found for \"%s\""), htmlspecialchars($artist)));

	echo Element\Tracklist::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo Element\Message::error($e->getMessage());
}
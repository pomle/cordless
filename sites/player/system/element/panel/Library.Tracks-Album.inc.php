<?
namespace Cordless;

use \Asenine\DB;

try
{
	if( !isset($_GET['album']) )
		throw New \Exception('No album name given');

	$album = $_GET['album'];

	echo Element\Library::head($album);

	$Fetch = new Fetch\UserTrack($User, 'byAlbum', $album);

	$userTracks = $Fetch();

	if( count($userTracks) == 0 )
		throw New \Exception(sprintf(_("No matches found for \"%s\""), htmlspecialchars($album)));

	echo Element\Tracklist::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo Element\Message::error($e->getMessage());
}
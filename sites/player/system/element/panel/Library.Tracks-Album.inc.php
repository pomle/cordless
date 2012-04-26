<?
namespace Cordless;

if( !isset($_GET['album']) )
	throw New PanelException('No album name given');

$album = $_GET['album'];

$Fetch = new Fetch\UserTrack($User, 'byAlbum', $album);
$userTracks = $Fetch();

echo
	Element\Library::head($album),
	Element\Tracklist::createFromUserTracks($userTracks);
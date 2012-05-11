<?
namespace Cordless;

if( !isset($_GET['albumID']) && !isset($_GET['album']) )
	throw New PanelException('No album ID or name given');


$albumIDs = isset($_GET['albumID']) ? array($_GET['albumID']) : null;

if( isset($_GET['album']) )
	$albumIDs = Album::getIDsFromName($_GET['album']);


if( !$albums = Album::loadFromDB($albumIDs) )
	throw New PanelException(_('Album(s) not found'));

$albumNames = array();
foreach($albums as $Album)
	$albumNames[] = $Album->title;

$title = join(', ', $albumNames);


$Fetch = new Fetch\UserTrack($User, 'byAlbum', $albumIDs);
$userTracks = $Fetch();

echo
	Element\Library::head($title),
	Element\Tracklist::createFromUserTracks($userTracks);
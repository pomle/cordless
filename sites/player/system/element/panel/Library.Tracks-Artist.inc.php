<?
namespace Cordless;

if( !isset($params->artist) )
	throw new PanelException('No artist name given');

$artist = $params->artist;

$Fetch = new Fetch\UserTrack($User, 'byArtist', $artist);
$userTracks = $Fetch();

echo
	Element\Library::head($artist),
	Element\Tracklist::createFromUserTracks($userTracks);
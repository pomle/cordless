<?
namespace Cordless;

$userID = array(isset($params->userID) ? $params->userID : $User->userID);

if( !isset($params->artist) )
	throw new PanelException('No artist name given');

$artist = $params->artist;

$Fetch = new Fetch\UserTrack($User, 'byArtist', $artist, $userID);
$userTracks = $Fetch();

echo
	Element\Library::head($artist),
	Element\Tracklist::createFromUserTracks($userTracks);
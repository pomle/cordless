<?
require '_Debug.php';

#if( !$playlistID || !$Playlist = \Music\Playlist::loadFromDB($playlistID) )
$Playlist = new \Music\Playlist();

#$Playlist = \Music\Playlist::loadFromDB(154);

print_r($Playlist);


#die();
#
$Playlist->title = "Pomles testplaylist";

$Fetcher = new \Fetch\UserTrack($User, 'bySearch', 'cinnamon');

$userTracks = $Fetcher();

foreach($userTracks as $UserTrack)
	$Playlist->addUserTrack($UserTrack);

\Music\Playlist::saveToDB($Playlist);

print_r($Playlist);


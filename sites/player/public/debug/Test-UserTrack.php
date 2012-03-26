<?
require '_Debug.php';

$userTrackIDs = \Asenine\DB::queryAndFetchArray("SELECT ID FROM Cordless_UserTracks ORDER BY RAND() LIMIT 5");

$userTracks = \Cordless\UserTrack::loadFromDB($userTrackIDs);

print_r($userTracks);
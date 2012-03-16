<?
require '_Debug.php';

$Track = \Music\Track::loadFromDB(3);

print_r($Track);

$UserTrack = new \Music\UserTrack($User->userID);
$UserTrack->setTrack($Track);

print_r($UserTrack);

\Music\UserTrack::saveToDB($UserTrack);

print_r($UserTrack);



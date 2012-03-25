<?
namespace Cordless;

function APIMethod($User, $params)
{
	$UserTrack = getUserTrack($params, $User);

	$timeNow = time();
	$lastFM_wasNotified = false;

	$artist = $UserTrack->artist;
	$title = $UserTrack->title;

	$duration = isset($params['duration']) ? $params['duration'] : null;

	if( isset($User->last_fm_username, $User->last_fm_key) && ($User->last_fm_scrobble === true) && ($LastFM = getLastFM()) )
	{
		$xml = $LastFM->updateNowPlaying($User->last_fm_key, $timeNow, $artist, $title, $duration);
		$lastFM_wasNotified = true;
	}

	return array(
		'userTrackID' => $UserTrack->userTrackID,
		'lastFM_wasNotified' => $lastFM_wasNotified
	);
}
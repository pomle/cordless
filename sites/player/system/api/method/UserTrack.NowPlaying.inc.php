<?
namespace Cordless;

function APIMethod($User, $params)
{
	$UserTrack = getUserTrack($params, $User, true, false);

	$timeNow = time();
	$lastFM_wasNotified = false;
	$lastFM_error = null;

	$artist = $UserTrack->artist;
	$title = $UserTrack->title;

	$duration = isset($params->duration) ? $params->duration : null;

	if( isset($User->last_fm_username, $User->last_fm_key) && ($User->last_fm_scrobble === true) && ($LastFM = getLastFM()) )
	{
		try
		{
			$xml = $LastFM->updateNowPlaying($User->last_fm_key, $timeNow, $artist, $title, $duration);
			$lastFM_wasNotified = true;
		}
		catch(\Asenine\API\LastFMException $e)
		{
			$lastFM_wasNotified = false;
			$lastFM_error = $e->getMessage();
		}
	}

	return array(
		'userTrackID' => $UserTrack->userTrackID,
		'lastFM_wasNotified' => $lastFM_wasNotified,
		'lastFM_error' => $lastFM_error
	);
}
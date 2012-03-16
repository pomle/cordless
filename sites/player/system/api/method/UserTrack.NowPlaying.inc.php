<?
namespace Cordless;

function APIMethod($User, $params)
{
	$timeNow = time();

	if( !isset($User->last_fm_username, $User->last_fm_key) || $User->last_fm_scrobble !== true )
		return "User not setup for scrobbling";


	if( isset($params['userTrackID']) && $UserTrack = getUserTrack($params, $User) )
	{
		$artist = $UserTrack->artist;
		$title = $UserTrack->title;
	}
	else
	{
		$artist = isset($params['artist']) ? $params['artist'] : null;
		$title = isset($params['title']) ? $params['title'] : null;
	}

	$duration = isset($params['duration']) ? $params['duration'] : null;

	$LastFM = getLastFM();
	$xml = $LastFM->updateNowPlaying($User->last_fm_key, $timeNow, $artist, $title, $duration);

	return;
}
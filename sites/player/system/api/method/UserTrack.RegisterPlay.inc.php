<?
namespace Cordless;

function APIMethod($User, $params)
{
	$timeNow = time();
	$cordless_doRegister = false;
	$lastFM_wasScrobbled = false;
	$lastFM_error = null;

	$UserTrack = getUserTrack($params, $User, true, false);

	$timePlayStart = isset($params->startTime) ? (int)$params->startTime : $timeNow;
	$duration = isset($params->duration) ? (float)$params->duration : null;
	$playedTime = isset($params->playedTime) ? (float)$params->playedTime : 0;

	$cordless_doRegister = ($playedTime > 60*4 || $playedTime > $duration / 2);
	$lastFM_doScrobble = ( $duration > 30 && ($playedTime > 60*4 || $playedTime > $duration / 2) );

	if( $cordless_doRegister && !$UserTrack->registerPlay($playedTime) )
		throw New \Exception('UserTrack::registerPlay() returned false');


	$artist = $UserTrack->artist;
	$title = $UserTrack->title;
	$album = $UserTrack->album;
	$trackNo = $UserTrack->trackNo;


	if( $lastFM_doScrobble && isset($User->last_fm_username, $User->last_fm_key) && ($User->last_fm_scrobble === true) && ($LastFM = getLastFM()) )
	{
		try
		{
			$xml = $LastFM->sendScrobble($User->last_fm_key, $timePlayStart, $artist, $title, $duration, $album);

			if( !$xml->xpath('/lfm[@status="ok"]') )
			{
				$e = $xml->xpath('/lfm/error');
				throw new \Asenine\API\LastFMException((string)$e[0]);
			}

			$lastFM_wasScrobbled = true;
		}
		catch(\Asenine\API\LastFMException $e)
		{
			$lastFM_wasScrobbled = false;
			$lastFM_error = $e->getMessage();
		}
	}

	return array(
		'userTrackID' => $UserTrack->userTrackID,
		'wasRegistered' => $cordless_doRegister,
		'lastFM_wasScrobbled' => $lastFM_wasScrobbled,
		'lastFM_error' => $lastFM_error,
	);
}
<?
namespace Cordless;

function APIMethod($User, $params)
{
	$timeNow = time();
	$cordless_doRegister = false;
	$lastFM_wasScrobbled = false;

	$UserTrack = getUserTrack($params, $User);

	$timePlayStart = isset($params['startTime']) ? (int)$params['startTime'] : $timeNow;
	$duration = isset($params['duration']) ? (float)$params['duration'] : null;
	$playedTime = isset($params['playedTime']) ? (float)$params['playedTime'] : 0;

	$cordless_doRegister = ($playedTime > 60*4 || $playedTime > $duration / 2);
	$lastFM_doScrobble = ( $duration > 30 && ($playedTime > 60*4 || $playedTime > $duration / 2) );

	if( $cordless_doRegister && !$UserTrack->registerPlay($playedTime) )
		throw New \Exception('UserTrack::registerPlay() returned false');


	$artist = $UserTrack->artist;
	$title = $UserTrack->title;


	if( $lastFM_doScrobble && isset($User->last_fm_username, $User->last_fm_key) && ($User->last_fm_scrobble === true) && ($LastFM = getLastFM()) )
	{
		$xml = $LastFM->sendScrobble($User->last_fm_key, $timePlayStart, $artist, $title, $duration);

		if( !$xml->xpath('/lfm[@status="ok"]') )
		{
			$e = $xml->xpath('/lfm/error');
			throw New APIException("Last.fm: " . (string)$e[0]);
		}

		$lastFM_wasScrobbled = true;
	}

	return array(
		'userTrackID' => $UserTrack->userTrackID,
		'wasRegistered' => $cordless_doRegister,
		'lastFM_wasScrobbled' => $lastFM_wasScrobbled
	);
}
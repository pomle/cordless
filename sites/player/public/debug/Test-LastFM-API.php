<?
require '_Debug.php';

$User;

header("Content-Type: text/html; charset=utf-8");

$Last = new \API\LastFM(LAST_FM_API_KEY, LAST_FM_API_SECRET);

$xml = $Last->sendScrobble($User->last_fm_key, time(), 'Pomle', 'Freakshow');

var_dump($xml);

#print_r($Last);
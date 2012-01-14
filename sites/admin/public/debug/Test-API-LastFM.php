<?
require '_Debug.inc.php';

$LastFM = new \API\LastFM(LAST_FM_API_KEY);

echo $LastFM->getArtistURL('Cinnamon Chasers');
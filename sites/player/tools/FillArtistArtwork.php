<?
require __DIR__ . '/_Common.inc.php';

use \Asenine\DB;

$query = DB::prepareQuery("SELECT ID FROM Cordless_Artists WHERE image_mediaID IS NULL");
$Result = DB::queryAndFetchResult($query);

$LastFM = \Cordless\getLastFM();

foreach ($Result as $row) {

	try {

		$Artist = \Cordless\Artist::loadFromDB($row['ID']);

		if ($Image = $LastFM->getArtistImage($Artist->name)) {
			$Artist->setImage($Image);
			print_r($Artist);
			\Cordless\Artist::saveToDB($Artist);
		}
	} catch (Exception $e) {
		echo "Failed on " . $row['ID'] . ", " . $e->getMessage() . "\n";
	}
}
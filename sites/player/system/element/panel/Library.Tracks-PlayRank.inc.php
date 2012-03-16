<?
namespace Cordless;

use \Asenine\DB;

try
{
	$timeStart = isset($_GET['uts_f']) ? $_GET['uts_f'] : time() - 60*60*24*7;
	$timeEnd = isset($_GET['uts_t']) ? $_GET['uts_t'] : time();

	echo Element\Library::head($title ?: _("Most Played"), sprintf(_("Between %s - %s"), \Asenine\Format::timestamp($timeStart, true), \Asenine\Format::timestamp($timeEnd, true)));

	$Fetcher = new Fetch\UserTrack($User, 'byPlayRank', $timeStart, $timeEnd);
	$Fetcher->limit = min(isset($_GET['limit']) ? (int)$_GET['limit'] : 20, 1000);

	$Tracklist = Element\Tracklist::createFromFetcher($Fetcher);

	if( $Tracklist->length == 0 )
		throw New \Exception(_("No tracks found"));

	echo $Tracklist;
}
catch(\Exception $e)
{
	echo Element\Message::error( $e->getMessage() );
}
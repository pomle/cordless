<?
namespace Cordless;

use \Asenine\DB;

try
{
	$timeStart = isset($_GET['uts_f']) ? $_GET['uts_f'] : null;
	$timeEnd = isset($_GET['uts_t']) ? $_GET['uts_t'] : null;

	echo Element\Library::head($title ?: _('Recently Added'));

	$Fetcher = new Fetch\UserTrack($User, 'byAddTime', $timeStart, $timeEnd);
	$Fetcher->limit = min(isset($_GET['limit']) ? (int)$_GET['limit'] : 20, 1000);

	echo Element\Tracklist::createFromFetcher($Fetcher);
}
catch(\Exception $e)
{
	echo Element\Message::error($e->getMessage());
}
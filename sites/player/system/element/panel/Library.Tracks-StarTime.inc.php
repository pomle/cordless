<?
namespace Cordless;

use \Asenine\DB;

try
{
	echo Element\Library::head(_('Recently Starred'));

	$Fetcher = new Fetch\UserTrack($User, 'byStarTime');
	$Fetcher->limit = min(isset($_GET['limit']) ? (int)$_GET['limit'] : 20, 1000);

	echo Element\Tracklist::createFromFetcher($Fetcher);
}
catch(\Exception $e)
{
	echo Element\Message::error($e->getMessage());
}
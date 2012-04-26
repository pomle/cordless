<?
namespace Cordless;

$timeStart = isset($params->uts_f) ? $params->uts_f : time() - 60*60*24*7;
$timeEnd = isset($params->uts_t) ? $params->uts_t : time();

$Fetcher = new Fetch\UserTrack($User, 'byPlayRank', $timeStart, $timeEnd);
$Fetcher->limit = min(isset($params->limit) ? (int)$params->limit : 20, 1000);

echo
	Element\Library::head(
		$params->title ?: _("Most Played"),
		sprintf(_("Between %s - %s"), \Asenine\Format::timestamp($timeStart, true), \Asenine\Format::timestamp($timeEnd, true))),
	Element\Tracklist::createFromFetcher($Fetcher);
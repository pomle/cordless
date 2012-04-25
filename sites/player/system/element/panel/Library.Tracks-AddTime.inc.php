<?
namespace Cordless;

$timeStart = isset($params->uts_f) ? $params->uts_f : null;
$timeEnd = isset($params->uts_t) ? $params->uts_t : null;


if( isset($params->userID) )
	$userIDs = array($params->userID);
else
	$userIDs = array($User->userID);


$Fetcher = new Fetch\UserTrack($User, 'byAddTime', $timeStart, $timeEnd, $userIDs);
$Fetcher->limit = min(isset($params->limit) ? (int)$params->limit : 20, 1000);


echo
	Element\Library::head($title ?: _('Recently Added')),
	Element\Tracklist::createFromFetcher($Fetcher);
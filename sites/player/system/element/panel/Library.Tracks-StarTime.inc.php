<?
namespace Cordless;


if( isset($params->userID) )
	$userIDs = array($params->userID);
else
	$userIDs = array($User->userID);


$Fetcher = new Fetch\UserTrack($User, 'byStarTime', null, null, $userIDs);
$Fetcher->limit = min(isset($params->limit) ? (int)$params->limit : 20, 1000);

echo
	Element\Library::head(_('Recently Starred')),
	Element\Tracklist::createFromFetcher($Fetcher);
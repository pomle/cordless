<?
namespace Cordless;

if( isset($params->userID) )
	$userIDs = array($params->userID);
elseif( isset($params->useFriends) )
	$userIDs = $User->getFriendUserIDs();
else
	$userIDs = array($User->userID);

$Fetcher = new Fetch\UserTrack($User, 'byPlayTime', $userIDs);
$Fetcher->limit = min(isset($params->limit) ? (int)$params->limit : 20, 1000);

echo
	Element\Library::head(_('Recently Played')),
	Element\Tracklist::createFromFetcher($Fetcher);
<?
namespace Cordless;

$userID = isset($params->userID) ? $params->userID : $User->userID;

$query = \Asenine\DB::prepareQuery("SELECT ID FROM Cordless_UserTracks WHERE userID = %d ORDER BY RAND()", $userID);

$Fetcher = new Fetch\UserTrack($User);
$Fetcher->limit = max(isset($params->limit) ? $params->limit : 20, 100);

$userTracks = $Fetcher->queryToUserTracks($query);

echo
	Element\Library::head(_("Random Tracks")),
	Element\Tracklist::createFromUserTracks($userTracks);
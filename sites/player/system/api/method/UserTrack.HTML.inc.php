<?
namespace Cordless;

function APIMethod(User $User, $params)
{
	list($userTrackIDs) = ensureParams($params, 'userTrackIDs');

	$userTrackIDs = (array)$userTrackIDs;

	$Fetch = new Fetch\UserTrack($User);

	$userTracks = $Fetch->getUserTracks($userTrackIDs);

	$response = array();

	foreach($userTracks as $UserTrack)
		$response[$UserTrack->userTrackID] = trim(preg_replace("/[\n\t]+/", " ", (string)Element\UserTrackItem::fromUserTrack($UserTrack)));

	return $response;
}
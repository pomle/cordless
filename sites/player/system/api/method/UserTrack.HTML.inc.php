<?
namespace Cordless;

function APIMethod(User $User, $params)
{
	list($userTrackIDs) = ensureParams($params, 'userTrackIDs');

	$userTracks = UserTrack::loadFromDB((array)$userTrackIDs);

	$response = array();

	foreach($userTracks as $UserTrack)
		$reponse[$UserTrack->userTrackID] = (string)Element\UserTrackItem::fromUserTrack($UserTrack);

	return 'blaha';

	return $response;
}
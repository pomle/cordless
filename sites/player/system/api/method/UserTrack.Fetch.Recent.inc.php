<?
namespace Cordless;

function APIMethod(User $User, $params)
{
	$limit = (int)(isset($params['limit']) ? $params['limit'] : 25);

	$Fetch = new \Fetch\UserTrack($User);
	$Fetch->limit = min($limit, 100);

	$userTracks = $Fetch->getRecent();

	return array('userTracks' => UserTrack\APIObject::convArray($userTracks));
}
<?
namespace Cordless;

function ensureLogin(User $User)
{
	if( $User->isLoggedIn() !== true )
	{
		header("HTTP/1.1 401 Access Denied");
		throw new APIException("Access Denied");
	}

	return;
}

function ensureParams($availableParams)
{
	$returnParams = array();
	$checkParams = array_slice(func_get_args(), 1);
	foreach($checkParams as $key)
	{
		if( !isset($availableParams->$key) ) throw new ParamException($key);

		$returnParams[] = $availableParams->$key;
	}

	return $returnParams;
}

function ensureSignature($User, $signature)
{
	if( !isset($User->sessionKey) )
		throw new \Exception('$User->sessionKey not set');

	$uts = mktime( date("H"), date("i"), 0, date("n"), date("j"), date("Y") );

	$validSignatures = array();

	for($delta = -1; $delta <= 1; $delta ++)
	{
		$f = $uts + (60 * $delta);
		$validSignatures[] = md5('COMMON_SALT' . $User->sessionKey . $f);
	}

	if( !in_array($signature, $validSignatures) )
	{
		header("HTTP/1.1 401 Access Denied");
		throw new APIException("Access Denied");
	}

	return;
}

function getUserTrack($params, User $User = null, $requireAccess = true, $requireOwner = true)
{
	ensureParams($params, 'userTrackID');

	if( !$UserTrack = UserTrack::loadFromDB($params->userTrackID, $User->userID) )
		throw new APIException("UserTrack not found");

	if( $User )
	{
		if( $requireAccess && !$UserTrack->isAccessible )
			throw new APIException("UserTrack Access Denied");

		if( $requireOwner && !$UserTrack->isOwner )
			throw new APIException("UserTrack Ownage Required");
	}

	return $UserTrack;
}

function jsonResponse($status, $data = null)
{
	die(json_encode(array('status' => $status === true ? true : false, 'data' => $data)));

	return;
}
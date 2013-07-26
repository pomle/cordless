<?
namespace Cordless;

function APIMethod($User, $params)
{
	if (isset($params->userTrackIDs)) {
		$query = \Asenine\DB::prepareQuery("UPDATE Cordless_Users SET state = %s WHERE userID = %d", json_encode($params), $User->userID);
		\Asenine\DB::query($query);
		return;
	}
	else {
		$query = \Asenine\DB::prepareQuery("SELECT state FROM Cordless_Users WHERE userID = %d", $User->userID);
		$state = \Asenine\DB::queryAndFetchOne($query);
		return json_decode($state);
	}
}
<?
namespace Cordless;

$userID = isset($params->userID) ? $params->userID : USER_ID;

if( isset($params->id_f) && isset($params->id_t) )
{
	$idF = $params->id_f;
	$idT = $params->id_t;

	$subtitle = sprintf(_("ID Interval (%d/%d)"), $idF, $idT);

	$query = \Asenine\DB::prepareQuery("SELECT
			ID
		FROM
			Cordless_UserTracks
		WHERE
			userID = %d
			AND ID BETWEEN %d AND %d",
		$User->userID,
		$idF,
		$idT);
}
else
{
	throw new PanelException("Missing arguments");
}

$Fetch = new Fetch\UserTrack($User);
$userTracks = $Fetch->queryToUserTracks($query);

echo
	Element\Library::head(_("Advanced Track List"), $subtitle),
	Element\Tracklist::createFromUserTracks($userTracks);
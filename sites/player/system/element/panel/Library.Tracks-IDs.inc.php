<?
namespace Cordless;

use \Asenine\DB;

try
{
	if( isset($_GET['id_f'], $_GET['id_t']) )
	{
		echo Element\Library::head(_("Advanced Track List"), sprintf(_("ID Interval (%d/%d)"), $_GET['id_f'], $_GET['id_t']));

		$query = DB::prepareQuery("SELECT ID FROM Cordless_UserTracks WHERE userID = %d AND ID BETWEEN %d AND %d", $User->userID, $_GET['id_f'], $_GET['id_t']);

		$Fetch = new Fetch\UserTrack($User);
		$userTracks = $Fetch->queryToUserTracks($query);
	}

	if( !isset($userTracks) || count($userTracks) == 0 )
		throw New \Exception(_("No tracks found"));

	echo Element\Tracklist::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo Element\Message::error( $e->getMessage() );
}
<?
namespace Cordless;

use \Asenine\DB;

try
{
	$query = DB::prepareQuery("SELECT ID FROM Cordless_UserTracks WHERE userID = %d ORDER BY RAND()", $User->userID);

	$Fetcher = new Fetch\UserTrack($User);
	$Fetcher->limit = max(isset($_GET['limit']) ? $_GET['limit'] : 20, 100);

	echo Element\Library::head(_("Random Tracks"));

	$userTracks = $Fetcher->queryToUserTracks($query);

	if( count($userTracks) == 0 )
		throw New \Exception(_("No tracks found"));

	echo Element\Tracklist::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo Element\Message::error( $e->getMessage() );
}
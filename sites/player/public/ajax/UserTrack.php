<?
namespace Cordless;

require '../../Init.Application.inc.php';

session_start();
require DIR_SITE_SYSTEM . 'init/User.inc.php';
session_write_close();

try
{
	$userTracks = array();

	if( isset($_GET['userTrackID']) )
	{
		$userTrackIDs = (array)$_GET['userTrackID'];

		$Fetcher = new Fetch\UserTrack($User);

		$userTracks = $Fetcher->getUserTracks($userTrackIDs);
	}

	ob_start();

	foreach($userTracks as $UserTrack)
		echo Element\UserTrackItem::fromUserTrack($UserTrack);

	echo ob_get_clean();

	die();
}
catch(\Exception $e)
{
	ob_end_clean();

	die($e->getMessage());
}
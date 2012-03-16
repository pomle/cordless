<?
namespace Cordless;

require '../../Init.Web.inc.php';

try
{
	if( isset($_GET['userTrackID']) )
	{
		$userTrackIDs = (array)$_GET['userTrackID'];
		$userTracks = UserTrack::loadFromDB($userTrackIDs);
	}

	if( !isset($userTracks) )
		throw New \Exception("No UserTracks found");

	ob_start();

	foreach($userTracks as $UserTrack)
		echo Element\UserTrackItem::fromUserTrack($UserTrack);

	echo ob_get_clean();
}
catch(\Exception $e)
{
	ob_end_clean();

	die($e->getMessage());
}

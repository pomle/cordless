<?
namespace Cordless;

use \Asenine\DB;

global $params;

if( !isset($params->userID) || !$User = User::loadFromDB($params->userID) )
	throw new PanelException('User not found');

echo Element\Library::head(false ? str_replace('%USERNAME%', $User->username, _('Home of %USERNAME%')) : $User->username);

$query = DB::prepareQuery("SELECT COUNT(*) FROM Cordless_UserTracks ut WHERE ut.userID = %u", $User->userID);
$userTrackCount = DB::queryAndFetchOne($query);

$query = DB::prepareQuery("SELECT SUM(playcount) FROM Cordless_UserTracks ut WHERE ut.userID = %u", $User->userID);
$userPlayCountTotal = DB::queryAndFetchOne($query);

?>
<div class="userOverview">

	<section class="status">
		<ul>
			<li><? echo htmlspecialchars(sprintf(_("Tracks in Library: %s"), formatCount($userTrackCount))); ?></li>
			<li><? echo htmlspecialchars(sprintf(_("Playcount: %s"), formatCount($userPlayCountTotal))); ?></li>
		</ul>
	</section>

	<?
	$userID = $User->userID;
	require DIR_ELEMENT . 'Block.Library.Browse.inc.php';
	?>

</div>
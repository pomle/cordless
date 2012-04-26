<?
namespace Cordless;

if( !isset($params->userID) || !$Friend = User::loadFromDB($params->userID) )
	throw new PanelException('User not found');

$query = \Asenine\DB::prepareQuery("SELECT COUNT(*) FROM Cordless_UserTracks ut WHERE ut.userID = %u", $Friend->userID);
$userTrackCount = \Asenine\DB::queryAndFetchOne($query);

$query = \Asenine\DB::prepareQuery("SELECT SUM(playcount) FROM Cordless_UserTracks ut WHERE ut.userID = %u", $Friend->userID);
$userPlayCountTotal = \Asenine\DB::queryAndFetchOne($query);

echo Element\Library::head(false ? str_replace('%USERNAME%', $Friend->username, _('Home of %USERNAME%')) : $Friend->username);
?>
<div class="userOverview<?
	if( $User->isFriend($Friend->userID) ) echo " isFriend";
	?>">

	<section class="control">
		<a href="<? echo apiLink('User.Friend', array('action' => 'toggle', 'friendUserID' => $Friend->userID)); ?>" class="apiCall">Befriend/Unfriend</a>
	</section>

	<section class="status">
		<ul>
			<li><? echo htmlspecialchars(sprintf(_("Tracks in Library: %s"), formatCount($userTrackCount))); ?></li>
			<li><? echo htmlspecialchars(sprintf(_("Playcount: %s"), formatCount($userPlayCountTotal))); ?></li>
		</ul>
	</section>

	<?
	$userID = $Friend->userID;
	require DIR_ELEMENT . 'Block.Library.Browse.inc.php';
	?>

</div>
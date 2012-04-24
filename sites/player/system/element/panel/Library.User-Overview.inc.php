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


$qs = sprintf('userID=%d', $User->userID);

?>
<div class="userOverview">

	<section class="status">
		<ul>
			<li><? echo htmlspecialchars(sprintf(_("Tracks in Library: %s"), formatCount($userTrackCount))); ?></li>
			<li><? echo htmlspecialchars(sprintf(_("Playcount: %s"), formatCount($userPlayCountTotal))); ?></li>
		</ul>
	</section>

	<section class="browse">
		<h3><? echo htmlspecialchars(_("Browse")); ?></h3>

		<ul>
			<li><? echo libraryLink(_("Albums"), 'Index-Albums', $qs); ?></li>
			<li><? echo libraryLink(_("Artists"), 'Index-Artists', $qs); ?></li>
			<li><? echo libraryLink(_("Playlists"), 'Index-Playlists', $qs); ?></li>
		</ul>

		<ul>
			<li><? echo libraryLink(_("Recently Added"), 'Tracks-AddTime', $qs); ?></li>
			<li><? echo libraryLink(_("Recently Played"), 'Tracks-PlayTime', $qs); ?></li>
			<li><? echo libraryLink(_("Recently Starred"), 'Tracks-StarTime', $qs); ?></li>
		</ul>

		<ul>
			<li><? echo libraryLink(_("Friends"), 'Index-Friends', $qs); ?></li>
			<li><? echo libraryLink(_("SmartPlaylists"), 'SmartPlaylists', $qs); ?></li>
		</ul>
	</section>

</div>
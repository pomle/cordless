<?
namespace Cordless;

use \Asenine\DB;

echo Element\Library::head(false ? str_replace('%USERNAME%', $User->username, _('Home of %USERNAME%')) : _('Home'));

$query = DB::prepareQuery("SELECT COUNT(*) FROM Cordless_UserTracks ut WHERE ut.userID = %u", $User->userID);
$userTrackCount = DB::queryAndFetchOne($query);

$query = DB::prepareQuery("SELECT SUM(playcount) FROM Cordless_UserTracks ut WHERE ut.userID = %u", $User->userID);
$userPlayCountTotal = DB::queryAndFetchOne($query);

$query = DB::prepareQuery("SELECT SUM(utp.duration) FROM Cordless_UserTracks ut JOIN Cordless_UserTrackPlays utp ON utp.userTrackID = ut.ID WHERE ut.userID = %u", $User->userID);
$userPlayDurationTotal = DB::queryAndFetchOne($query);

$isLastFmAvailable = (bool)LAST_FM_API_KEY;
$isLastFmConnected = ($isLastFmAvailable && $User->last_fm_username);

if( $isLastFmAvailable )
{
	$urlLastFmConnect = 'http://www.last.fm/api/auth?api_key=' . LAST_FM_API_KEY;
	$urlLastFmConnect = 'http://www.last.fm/api/auth?api_key=' . LAST_FM_API_KEY . '&cb=' . URL_PLAYER . 'helper/LastFM.Callback.php';
}

?>
<div class="home">

	<section class="status">
		<h3><img src="./img/Cordless_Last.fm-Icon.png" style="vertical-align: text-bottom;"> <? echo htmlspecialchars(_($User->username)); ?></h3>

		<ul>
			<li><? echo htmlspecialchars(sprintf(_("Tracks in Library: %s"), formatCount($userTrackCount))); ?></li>
			<li><? echo htmlspecialchars(sprintf(_("Playcount: %s"), formatCount($userPlayCountTotal))); ?></li>
			<li><? echo htmlspecialchars(sprintf(_("Playduration: %s"), formatDuration($userPlayDurationTotal))); ?></li>
			<li><? echo libraryLink(_("Settings"), 'Settings-Cordless'); ?></li>
		</ul>
	</section>

	<section class="quickplay">
		<h3><? echo htmlspecialchars(_("Quick Play")); ?></h3>

		<ul>
			<li><? echo actionLink(_("Random"), 'Cordless.Player.playbackStart();'); ?></li>

		</ul>
	</section>

	<section class="browse">
		<h3><? echo htmlspecialchars(_("Browse")); ?></h3>

		<ul>
			<li><? echo libraryLink(_("Albums"), 'Index-Albums'); ?></li>
			<li><? echo libraryLink(_("Artists"), 'Index-Artists'); ?></li>
			<li><? echo libraryLink(_("Playlists"), 'Index-Playlists'); ?></li>
		</ul>

		<ul>
			<li><? echo libraryLink(_("Recently Added"), 'Tracks-AddTime', 'limit=25'); ?></li>
			<li><? echo libraryLink(_("Recently Played"), 'Tracks-PlayTime', 'limit=25'); ?></li>
			<li><? echo libraryLink(_("Recently Starred"), 'Tracks-StarTime', 'limit=25'); ?></li>
		</ul>

		<ul>
			<li><? echo libraryLink(_("Friends"), 'Index-Friends'); ?></li>
			<li><? echo libraryLink(_("SmartPlaylists"), 'SmartPlaylists'); ?></li>
		</ul>
	</section>

	<?
	if( $isLastFmConnected )
	{
		$lastHomeURL = 'http://www.last.fm/user/' . htmlspecialchars($User->last_fm_username);

		?>
		<section class="lastfm_status">
			<h3><img src="./img/Last.fm-Frontpage-Icon.png" style="vertical-align: text-bottom;"> <? echo htmlspecialchars($User->last_fm_username); ?></h3>

			<ul id="last_fm_status" data-lastfm-username="<? echo htmlspecialchars($User->last_fm_username); ?>">
				<li><a href="<? echo $lastHomeURL; ?>" target="last_fm_userhome"><? echo _("Last.fm Home"); ?></a></li>
				<li class="playcount"><? echo _('Plays'); ?>:&nbsp;</li>
			</ul>

			<ul>
				<li><? echo libraryLink(_("Settings"), 'Settings-Lastfm'); ?></li>
				<li><a href="<? echo $urlLastFmConnect; ?>" target="last_fm_connect"><? echo _("Reconnect"); ?></a></li>
			<ul>

			<script type="text/javascript">
				$(function() {
					LastFM.updateHomeStatus();
				});
			</script>
		</section>
		<?
	}
	?>


	<section class="misc">
		<h3><? echo htmlspecialchars(_("Misc")); ?></h3>

		<?
		if( $isLastFmAvailable && !$isLastFmConnected )
		{
			?>
			<ul>
				<li><a href="<? echo $urlLastFmConnect; ?>" target="last_fm_connect"><img src="./img/Last.fm-Frontpage-Icon.png" style="vertical-align: text-bottom;"> <? echo _("Connect to Last.fm"); ?></a></li>
			</ul>
			<?
		}
		?>

		<ul>
			<li><a href="#" onclick="javascript:Cordless.Interface.importQueueOpen(); return false;"><? echo _("Upload"); ?></a></li>
			<li><? echo libraryLink(_("Advanced Track Import"), 'Import'); ?></li>
		</ul>

		<ul>
			<li><a href="/Logout.php"><? echo _("Logout"); ?></a></li>
		</ul>
	</section>

</div>


<?
namespace Cordless;

use \Asenine\DB;

echo Element\Library::head(_('Most Played'));

$query = DB::prepareQuery("SELECT utp.timeCreated FROM Cordless_UserTrackPlays utp JOIN Cordless_UserTracks ut ON ut.ID = utp.userTrackID WHERE ut.userID = %d ORDER BY ID ASC LIMIT 1", $User->userID);
$timeUntil = (int)DB::queryAndFetchOne($query) ?: null;

if( $timeUntil )
{
	?>

	<ul>
		<li><? echo libraryLink($t = _('Today'), 'Tracks-PlayRank', sprintf('uts_f=%d&uts_t=%d&title=%s', strtotime('today'), strtotime('tomorrow'), urlencode($t))); ?></li>
		<li><? echo libraryLink($t = _('24 hours back'), 'Tracks-PlayRank', sprintf('uts_f=%d&uts_t=%d&title=%s', strtotime('-1 days'), time(), urlencode($t))); ?></li>
		<li><? echo libraryLink($t = _('Last week'), 'Tracks-PlayRank', sprintf('uts_f=%d&uts_t=%d&title=%s', strtotime('today -1 weeks'), strtotime('tomorrow'), urlencode($t))); ?></li>
		<li><? echo libraryLink($t = _('Last month'), 'Tracks-PlayRank', sprintf('uts_f=%d&uts_t=%d&title=%s', strtotime('today -1 months'), strtotime('tomorrow'), urlencode($t))); ?></li>
		<li><? echo libraryLink($t = _('Last 3 months'), 'Tracks-PlayRank', sprintf('uts_f=%d&uts_t=%d&title=%s', strtotime('today -3 months'), strtotime('tomorrow'), urlencode($t))); ?></li>
	</ul>

	<ul>
		<?
		$iYear = 0;
		while($iYear < 5)
		{
			?>
			<li>
				<?
				$year = date('Y') - $iYear;

				$yearStart = mktime(0,0,0,1,1,$year);
				$yearEnd = mktime(0,0,0,1,1,$year+1);

				echo libraryLink($t1 = date('Y', $yearStart), 'Tracks-PlayRank', sprintf('uts_f=%d&uts_t=%d&title=%s', $yearStart, $yearEnd, urlencode($t1)));

				$iMonth = 0;
				while($iMonth++ < 12)
				{
					$monthStart = mktime(0,0,0,$iMonth,1,$year);
					$monthEnd = mktime(0,0,0,$iMonth+1,1,$year);
					echo " &raquo; ", libraryLink($t2 = date('F', $monthStart), 'Tracks-PlayRank', sprintf('uts_f=%d&uts_t=%d&title=%s', $monthStart, $monthEnd, urlencode($t1.' '.$t2)));
				}
				?>
			</li>
			<?
			$iYear++;
		}
		?>
	</ul>
	<?
}
else
{
	?>
	<p>
		<? echo _("You haven't played anything yet."); ?>
	</p>
	<?
}
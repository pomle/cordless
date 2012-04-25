<?
namespace Cordless;

use \Asenine\DB;

$userID = isset($params->userID) ? $params->userID : $User->userID;

echo Element\Library::head(_('Added During'));
?>
<ul>
	<?
	### Tracks newer than 24 h
	$timeNow = time();
	$timeToday = mktime(0,0,0, date('n'), date('j') + 1, date('Y')); ### Actually 00:00 tonight

	$query = DB::prepareQuery("SELECT
			COUNT(*) AS trackCount
		FROM
			Cordless_UserTracks ut
		WHERE
			ut.userID = %u
			AND ut.timeCreated > %d",
		$userID,
		$timePre24h = ($timeNow - 60*60*24*1));

	$trackCount = DB::queryAndFetchOne($query);

	if( $trackCount )
	{
		?>
		<li><? echo libraryLink($t = _('Last 24 hours'), 'Tracks-AddTime', sprintf('uts_f=%d&uts_t=%d&title=%s', $timePre24h, $timeNow, urlencode($t))); ?> (<? echo $trackCount; ?>)</li>
		<?
	}

	### Tracks newer than 7 days

	$timePre7d = strtotime("-1 weeks");

	$query = DB::prepareQuery("SELECT
			COUNT(*) AS trackCount
		FROM
			Cordless_UserTracks ut
		WHERE
			ut.userID = %u
			AND ut.timeCreated > %d",
		$userID,
		$timePre7d);

	$trackCount = DB::queryAndFetchOne($query);

	if( $trackCount )
	{
		?>
		<li><? echo libraryLink($t = _('Last 7 days'), 'Tracks-AddTime', sprintf('uts_f=%d&uts_t=%d&title=%s', $timePre7d, $timeNow, urlencode($t))); ?> (<? echo $trackCount; ?>)</li>
		<?
	}


	### Grab Year and Months which had uploads
	$query = DB::prepareQuery("SELECT
			YEAR(FROM_UNIXTIME(ut.timeCreated)) AS year,
			MONTH(FROM_UNIXTIME(ut.timeCreated)) AS month,
			COUNT(*) AS trackCount
		FROM
			Cordless_UserTracks ut
		WHERE
			ut.userID = %u
		GROUP BY
			year,
			month
		ORDER BY
			year DESC,
			month DESC",
		$userID);

	$Result = DB::queryAndFetchResult($query);

	foreach($Result as $row)
	{
		$uts_f = mktime(0, 0, 0, $row['month'], 1, $row['year']);
		$uts_t = mktime(0, 0, 0, $row['month'] + 1, 1, $row['year']);
		?>
		<li><? echo libraryLink($t = strftime('%Y %B', $uts_f), 'Tracks-AddTime', sprintf('uts_f=%d&uts_t=%d&title=%s', $uts_f, $uts_t, urlencode($t))); ?> (<? echo $row['trackCount']; ?>)</li>
		<?
	}
	?>
</ul>
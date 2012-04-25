<?
namespace Cordless;

$userID = isset($params->userID) ? $params->userID : $User->userID;

$timeGap = 60*15;
$trackLimit = 1000;

$query = \Asenine\DB::prepareQuery("SELECT
		ut.ID AS userTrackID,
		ut.timeCreated
	FROM
		Cordless_UserTracks ut
	WHERE
		ut.userID = %d
	ORDER BY
		ut.ID DESC
	LIMIT %d",
	$userID,
	$trackLimit);

$Result = \Asenine\DB::queryAndFetchResult($query);

echo Element\Library::head(_('Upload Events'), sprintf(_("Last %d added tracks grouped by time separated by minimum of %d minutes"), $trackLimit, floor($timeGap / 60)));
?>
<ul>
	<?
	$trackCount = 0;
	$resultLen = $Result->rowCount();
	$timeRowPrev = null;
	$i = 0;
	foreach($Result as $row)
	{
		if( !isset($utID_first) ) $utID_first = $row['userTrackID'];
		$i++;
		$trackCount++;
		$timeRow = (int)$row['timeCreated'];

		if( $i == $resultLen || ( $timeRowPrev && ($timeRowPrev - $timeRow) > $timeGap ) )
		{
			$utID_last = $row['userTrackID'];
			?>
			<li><? echo libraryLink(\Asenine\Format::timestamp($timeRow, true), 'Tracks-IDs', sprintf('id_f=%d&id_t=%d', $utID_last, $utID_first)); ?> (<? echo $trackCount; ?>)</li>
			<?
			$trackCount = 0;
			unset($utID_first);
		}
		$timeRowPrev = $timeRow;
	}
	?>
</ul>
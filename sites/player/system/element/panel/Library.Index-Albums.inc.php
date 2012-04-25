<?
namespace Cordless;

$userID = isset($params->userID) ? $params->userID : $User->userID;

$query = \Asenine\DB::prepareQuery("SELECT
		IFNULL(ut.album, a.title) AS title,
		IFNULL(ut.timeReleased, a.timeReleased) AS timeReleased,
		COUNT(*) AS trackCount
	FROM
		Cordless_UserTracks ut
		JOIN Cordless_AlbumTracks at ON at.trackID = ut.trackID
		JOIN Cordless_Albums a ON a.ID = at.albumID
	WHERE
		ut.userID = %u
	GROUP BY
		a.ID
	ORDER BY
		title ASC",
	$userID);

$Result = \Asenine\DB::queryAndFetchResult($query);
$len = $Result->rowCount();


echo Element\Library::head(_('Albums'));
?>
<div class="indexlist">
	<?
	$symbol_current = null;

	$i = 0;
	foreach($Result as $album)
	{
		$i++;
		$symbol = mb_strtoupper(mb_substr($album['title'], 0, 1));

		if( preg_match("/^[0-9]/", $symbol) )
			$symbol = "0-9";

		if( $symbol != $symbol_current )
		{
			if( $symbol_current ) echo '</ul>';

			$symbol_current = $symbol;
			printf('<h2>%s</h2>', $symbol_current);
			echo '<ul>';
		}

		printf('<li>%s (%d)</li>', libraryLink(htmlspecialchars($album['title']), 'Tracks-Album', 'album=' . urlencode($album['title'])), $album['trackCount']);

		if( $i == $len ) echo '</ul>';
	}
	?>
</div>
<?
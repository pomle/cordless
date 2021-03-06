<?
namespace Cordless;

$userID = isset($params->userID) ? $params->userID : $User->userID;

$query = \Asenine\DB::prepareQuery("SELECT
		artist AS name,
		COUNT(*) AS trackCount
	FROM
		Cordless_UserTracks
	WHERE
		userID = %u
	GROUP BY
		artist
	ORDER BY
		artist ASC",
	$userID);

$Result = \Asenine\DB::queryAndFetchResult($query);
$len = $Result->rowCount();

echo Element\Library::head(_('Artists'));

?>
<div class="indexlist">
	<?
	$symbol_current = null;

	$i = 0;
	foreach($Result as $artist)
	{
		$i++;
		$symbol = mb_strtoupper(mb_substr($artist['name'], 0, 1));

		if( $symbol != $symbol_current )
		{
			if( $symbol_current ) echo '</ul>';

			$symbol_current = $symbol;
			printf('<h2>%s</h2>', $symbol_current);
			echo '<ul>';
		}

		printf('<li>%s (%d)</li>', libraryLink(htmlspecialchars($artist['name']), 'Tracks-Artist', array('artist' => $artist['name'], 'userID' => $userID)), $artist['trackCount']);

		if( $i == $len ) echo '</ul>';
	}
	?>
</div>
<?
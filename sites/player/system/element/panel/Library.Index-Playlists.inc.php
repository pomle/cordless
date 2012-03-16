<?
namespace Cordless;

use \Asenine\DB;

echo Element\Library::head(_('Playlists'));

$query = DB::prepareQuery("SELECT
		p.ID AS playlistID,
		p.title,
		COUNT(pt.ID) AS trackCount
	FROM
		Cordless_Playlists p
		JOIN Cordless_UserPlaylists up ON up.playlistID = p.ID
		LEFT JOIN Cordless_PlaylistTracks pt ON pt.playlistID = p.ID
	WHERE
		up.userID = %u
	GROUP BY
		p.ID
	ORDER BY
		title ASC",
	$User->userID);

$Result = DB::queryAndFetchResult($query);
$len = $Result->rowCount();

?>
<div class="indexlist">
	<?
	$symbol_current = null;

	$i = 0;
	foreach($Result as $playlist)
	{
		$i++;
		$symbol = mb_strtoupper(mb_substr($playlist['title'], 0, 1));

		if( $symbol != $symbol_current )
		{
			if( $symbol_current ) echo '</ul>';

			$symbol_current = $symbol;
			printf('<h2>%s</h2>', $symbol_current);
			echo '<ul>';
		}

		printf('<li>%s (%d)</li>', libraryLink(htmlspecialchars($playlist['title']), 'Tracks-Playlist', sprintf('playlistID=%d', $playlist['playlistID'])), $playlist['trackCount']);

		if( $i == $len ) echo '</ul>';
	}
	?>
</div>
<?
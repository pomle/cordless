<?
namespace Cordless;

class SearchException extends \Exception{}

try
{
	$search = $params->q;

	### Handle special search
	if( preg_match('/^(.+):(.+)$/U', $search, $match) )
	{
		list(, $action, $arg) = $match;

		switch( strtolower($action) )
		{
			case 'user':
				if( $params->userID = \Asenine\User\Dataset::getUserID($arg) )
				{
					libraryPanel('User-Overview', $params);
					exit;
				}

				$params->search = $arg;
				libraryPanel('Index-Friends', $params);
				exit;
			break;

			case 'usertrack':
				$params->userTrackID = (int)$arg;
				libraryPanel('UserTrack-Control', $params);
				exit;
			break;
		}
	}


	### Standard search (tracks and albums)
	$queryMinLen = 2;
	$searchPreviewMaxLen = 8; ### Lenght of query in text displayed in trail
	$wildcards = array(' ', '*', '%');

	$albumsMax = 10; ### Length of album list
	$artistsMax = 3; ### How many album artists that will be displayed after album names


	$title = _("Search");
	if( mb_strlen($search) > 0 ) $title .= sprintf(" (%s)", mb_strlen($search) > $searchPreviewMaxLen + 3 ? mb_substr($search, 0, $searchPreviewMaxLen) . '...' : $search);

	echo Element\Library::head(
		_('Search'),
		$search,
		$title
	);

	if( strlen(str_replace($wildcards, '', $search)) < $queryMinLen )
		throw new SearchException(sprintf(_("Query too short. Minimum allowed length is %d characters"), $queryMinLen));

	$query_search = str_replace($wildcards, '%', $search);

	$userIDs = array_merge(array(USER_ID), $User->getFriendUserIDs()); ### Current user ID plus users that have befriended current user


	### Look for albums
	$query = \Asenine\DB::prepareQuery("SELECT
			a.ID AS albumID
		FROM
			Cordless_UserTracks ut
			JOIN Cordless_Tracks t ON t.ID = ut.trackID
			JOIN Cordless_AlbumTracks at ON at.trackID = t.ID
			JOIN Cordless_Albums a ON a.ID = at.albumID
		WHERE
			(
				CONCAT_WS(' ', ut.artist, ut.title) LIKE %S
				OR a.title LIKE %S
			)
			AND ut.userID IN %a
		GROUP BY
			a.ID
		ORDER BY
			SUM(ut.playcount) DESC",
		$query_search,
		$query_search,
		$userIDs);

	$albumIDs = \Asenine\DB::queryAndFetchArray($query);



	$Fetcher = new Fetch\UserTrack($User, 'bySearch', $query_search, $userIDs);
	$Fetcher->limit = 20;

	$Tracklist = Element\Tracklist::createFromFetcher( $Fetcher );

	if( $Tracklist->length == 0 && count($albumIDs) == 0 )
		throw new SearchException(sprintf(_("No matches found for \"%s\""), htmlspecialchars($search)));


	?>
	<div class="search">
		<?
		if( $Tracklist->length > 0 )
		{
			?>
			<section class="tracks">

				<h2><? echo _('Tracks'); ?></h2>

				<? echo $Tracklist; ?>

			</section>
			<?
		}

		if( count($albumIDs) > 0 )
		{
			$albums = Album::loadFromDB($albumIDs);
			?>
			<section class="albums">

				<h2><? echo _('Albums'); ?></h2>

				<ul class="albums">
					<?
					$albumCount = 0;
					while($Album = array_shift($albums))
					{
						$albumCount++;

						if( is_numeric($albumsMax) && $albumCount >= $albumsMax )
						{
							printf(_("...and %d more hidden"), count($albums) + 1);
							break;
						}

						$artistString = '';
						if( $artists = $Album->getArtists() )
						{
							$artistNames = array();
							while(count($artistNames) < $artistsMax && $Artist = array_shift($artists) )
								$artistNames[] = $Artist->name;

							$artistString = join(', ', $artistNames);

							if( ($artistsLeft = count($artists)) > 0 )
								$artistString .= '... ' . sprintf(_('%d more'), $artistsLeft);
						}

						?>
						<li>
							<? echo libraryLink($Album->title, 'Tracks-Album', array('albumID' => $Album->albumID)); ?>
							<span class="artists">(<? echo $artistString; ?>)</span>
						</li>
						<?
					}
					?>
				</ul>

			</section>
			<?
		}
	?>
	</div>
	<?
}
catch(SearchException $e)
{
	printf('<p>%s</p>', $e->getMessage());
}
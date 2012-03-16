<?
namespace Cordless;

use \Asenine\DB;

$queryMinLen = 2;
$searchPreviewMaxLen = 8;
$wildcards = array(' ', '*', '%');

try
{
	$search = $_GET['q'];

	$title = _("Search");
	if( mb_strlen($search) > 0 ) $title .= sprintf(" (%s)", mb_strlen($search) > $searchPreviewMaxLen + 3 ? mb_substr($search, 0, $searchPreviewMaxLen) . '...' : $search);

	echo Element\Library::head(
		_('Search'),
		$search,
		$title
	);

	if( strlen(str_replace($wildcards, '', $search)) < $queryMinLen )
		throw New \Exception(sprintf(_("Query too short. Minimum allowed length is %d _real_ characters"), $queryMinLen));

	$query_search = str_replace($wildcards, '%', $search);

	$Fetcher = new Fetch\UserTrack($User, 'bySearch', $query_search);
	$Fetcher->limit = 20;

	$Tracklist = Element\Tracklist::createFromFetcher( $Fetcher );

	if( $Tracklist->length == 0 )
		throw New \Exception(sprintf(_("No matches found for \"%s\""), htmlspecialchars($search)));

	echo $Tracklist;
}
catch(\Exception $e)
{
	echo Element\Message::error($e->getMessage());
}
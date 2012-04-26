<?
namespace Cordless;

class SearchException extends \Exception{}

try
{
	$queryMinLen = 2;
	$searchPreviewMaxLen = 8;
	$wildcards = array(' ', '*', '%');

	$search = $params->q;

	if( preg_match('/^cordless:(.+):(.+)$/U', $search, $match) )
	{
		list(, $action, $arg) = $match;

		switch( strtolower($action) )
		{
			case 'user':
				if( !$params->userID = \Asenine\User\Dataset::getUserID($arg) )
					throw new PanelException(str_replace('%USERNAME%', $arg, _('User "%USERNAME%" not found')));

				libraryPanel('User-Overview', $params);
				exit;
			break;

			case 'usertrack':
				$params->userTrackID = (int)$arg;
				libraryPanel('UserTrack-Control', $params);
				exit;
			break;
		}
	}



	$title = _("Search");
	if( mb_strlen($search) > 0 ) $title .= sprintf(" (%s)", mb_strlen($search) > $searchPreviewMaxLen + 3 ? mb_substr($search, 0, $searchPreviewMaxLen) . '...' : $search);

	echo Element\Library::head(
		_('Search'),
		$search,
		$title
	);

	if( strlen(str_replace($wildcards, '', $search)) < $queryMinLen )
		throw new SearchException(sprintf(_("Query too short. Minimum allowed length is %d _real_ characters"), $queryMinLen));

	$query_search = str_replace($wildcards, '%', $search);

	$userIDs = array_merge(array(USER_ID), $User->getFriendUserIDs());


	$Fetcher = new Fetch\UserTrack($User, 'bySearch', $query_search, $userIDs);
	$Fetcher->limit = 20;

	$Tracklist = Element\Tracklist::createFromFetcher( $Fetcher );

	if( $Tracklist->length == 0 )
		throw new SearchException(sprintf(_("No matches found for \"%s\""), htmlspecialchars($search)));

	echo $Tracklist;
}
catch(SearchException $e)
{
	printf('<p>%s</p>', $e->getMessage());
}
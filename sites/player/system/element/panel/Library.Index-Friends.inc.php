<?
namespace Cordless;

if( isset($params->search) )
{
	$title = str_replace("%SEARCH_STRING%", $params->search, _('User Search for "%SEARCH_STRING%"'));

	$query = \Asenine\DB::prepareQuery("SELECT
			cu.userID
		FROM
			Cordless_Users cu
			JOIN Asenine_Users au ON au.ID = cu.userID
		WHERE
			au.username LIKE %S",
		$params->search);
}
else
{
	$title = _('Friends');

	$userID = isset($params->userID) ? $params->userID : $User->userID;

	$query = \Asenine\DB::prepareQuery("SELECT
			uf.friendUserID AS userID
		FROM
			Cordless_UserFriends uf
			JOIN Asenine_Users u ON u.ID = uf.friendUserID
		WHERE
			uf.userID = %d
		ORDER BY
			u.username ASC",
		$userID);
}

$userIDs = \Asenine\DB::queryAndFetchArray($query);

$users = User::loadFromDB($userIDs);

echo Element\Library::head($title);
?>
<ul>
	<?
	foreach($users as $User)
		printf('<li>%s</li>', libraryLink($User->username, 'User-Overview', sprintf('userID=%d', $User->userID)));
	?>
</ul>
<?
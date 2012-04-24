<?
namespace Cordless;

use \Asenine\DB;

$query = DB::prepareQuery("SELECT
		uf.friendUserID AS userID
	FROM
		Cordless_UserFriends uf
		JOIN Asenine_Users u ON u.ID = uf.friendUserID
	WHERE
		uf.userID = %d
	ORDER BY
		u.username ASC",
	$User->userID);

$userIDs = DB::queryAndFetchArray($query);

$users = User::loadFromDB($userIDs);

echo Element\Library::head(_('Friends'));

?>
<ul>
	<?
	foreach($users as $User)
		printf('<li>%s</li>', libraryLink($User->username, 'User-Overview', sprintf('userID=%d', $User->userID)));
	?>
</ul>
<?
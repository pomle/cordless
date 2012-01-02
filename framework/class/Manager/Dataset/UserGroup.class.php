<?
namespace Manager\Dataset;

class UserGroup extends _Common
{
	const TABLE = 'UserGroups';

	public static function getAvailable()
	{
		$query = "SELECT ID FROM UserGroups ORDER BY name ASC";
		return \DB::queryAndFetchArray($query);
	}

	public static function getIDsFromLabel($userGroupLabel)
	{
		$query = \DB::prepareQuery("SELECT ID FROM UserGroups WHERE label = %s", $userGroupLabel);
		return \DB::queryAndFetchOne($query);
	}

	public static function getProperties($userGroupID)
	{
		$query = \DB::prepareQuery("SELECT * FROM UserGroups WHERE ID = %u", $userGroupID);
		return \DB::queryAndFetchOne($query);
	}

	public static function getUserIDs($userGroupIDs)
	{
		$query = \DB::prepareQuery("SELECT userID FROM UserGroupUsers WHERE userGroupID IN %a", $userGroupIDs);
		return \DB::queryAndFetchArray($query);
	}

	public static function getUserIDsFromLabel($userGroupLabel)
	{
		$userGroupIDs = self::getIDsFromLabel($userGroupLabel);
		$userIDs = self::getUserIDs($userGroupIDs);
		return $userIDs;
	}
}
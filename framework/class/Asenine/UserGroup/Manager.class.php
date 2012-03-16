<?
namespace Asenine\UserGroup;

use \Asenine\DB;

class Manager
{
	public static function addToDB()
	{
		$query = "INSERT INTO UserGroups (ID, timeCreated) VALUES(NULL, UNIX_TIMESTAMP())";
		$userGroupID = (int)DB::queryAndGetID($query);
		return $userGroupID;
	}
}
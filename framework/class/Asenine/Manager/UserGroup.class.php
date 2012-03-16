<?
namespace Asenine\Manager;

class UserGroup extends Common\DB
{
	public static function addToDB()
	{
		$query = "INSERT INTO UserGroups (ID, timeCreated) VALUES(NULL, UNIX_TIMESTAMP())";
		$userGroupID = (int)\DB::queryAndGetID($query);
		return $userGroupID;
	}

	public static function loadFromDB($userGroupIDs)
	{
		$userGroups = array();

		$query = \DB::prepareQuery("SELECT
				ug.ID AS userGroupID,
				ug.name,
				ug.label,
				ug.description,
				ug.isTaskAssignable
			FROM
				UserGroups ug
			WHERE
				ug.ID IN %a", $userGroupIDs);

		$result = \DB::queryAndFetchResult($query);

		while($userGroup = \DB::assoc($result))
		{
			$UserGroup = new \stdClass();

			$UserGroup->userGroupID = (int)$userGroup['userGroupID'];
			$UserGroup->name = $userGroup['name'];
			$UserGroup->label = $userGroup['label'] ?: null;
			$UserGroup->description = $userGroup['description'];
			$UserGroup->isTaskAssignable = (bool)$userGroup['isTaskAssignable'];

			$userGroups[$UserGroup->userGroupID] = $UserGroup;
		}

		return $userGroups;
	}
}
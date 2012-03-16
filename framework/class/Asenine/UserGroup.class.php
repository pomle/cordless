<?
namespace Asenine;

class UserGroup
{
	public static function loadFromDB($userGroupIDs)
	{
		if( !$returnArray = is_array($userGroupIDs) )
			$userGroupIDs = (array)$userGroupIDs;

		$userGroups = array_fill_keys($userGroupIDs, false);

		$query = DB::prepareQuery("SELECT
				ug.ID AS userGroupID,
				ug.name,
				ug.label,
				ug.description,
				ug.isTaskAssignable
			FROM
				UserGroups ug
			WHERE
				ug.ID IN %a",
			$userGroupIDs);

		$result = DB::queryAndFetchResult($query);

		while($userGroup = DB::assoc($result))
		{
			$UserGroup = new self();

			$UserGroup->userGroupID = (int)$userGroup['userGroupID'];
			$UserGroup->name = $userGroup['name'];
			$UserGroup->label = $userGroup['label'] ?: null;
			$UserGroup->description = $userGroup['description'];
			$UserGroup->isTaskAssignable = (bool)$userGroup['isTaskAssignable'];

			$userGroups[$UserGroup->userGroupID] = $UserGroup;
		}

		$userGroups = array_filter($userGroups);

		return $returnArray ? $userGroups : reset($userGroups);
	}
}
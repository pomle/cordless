<?
use
	\Asenine\DB,
	\Asenine\UserGroup,
	\Asenine\UserGroup\Dataset,
	\Asenine\UserGroup\Manager;

class UserGroupIO extends AjaxIO
{
	private function ensureExistence()
	{
		if( !$UserGroup = UserGroup::loadFromDB($this->userGroupID) )
			throw New Exception(_('Grupp existerar inte'));
	}

	public function saveGeneral()
	{
		global $User;
		ensurePolicies('AllowEditUserGroup');

		$this->ensureExistence();

		$this->importArgs('name', 'label', 'description', 'isTaskAssignable');

		$query = DB::prepareQuery("UPDATE
				Asenine_UserGroups
			SET
				name = %s,
				label = NULLIF(%s, ''),
				description = %s,
				isTaskAssignable = %u
			WHERE
				ID = %u",
			$this->name,
			$this->label,
			$this->description,
			$this->isTaskAssignable,
			$this->userGroupID);

		DB::queryAndCountAffected($query);

		$this->loadUserGroup();

		Message::addNotice(_('Grupp sparad'));
	}

	public function savePolicies()
	{
		global $User;
		ensurePolicies('AllowEditUserGroup');

		$this->ensureExistence();

		$this->importArgs('policyIDs');

		// Only delete policies that current user has power over or all if is Administrator
		$allowedPolicyIDs = \Asenine\User\Dataset::getPolicies(USER_ID);

		$query = DB::prepareQuery("DELETE FROM Asenine_UserGroupPolicies WHERE userGroupID = %u", $this->userGroupID);

		if( !USER_IS_ADMIN )
			$query .= DB::prepareQuery(" AND policyID IN %a", $allowedPolicyIDs);

		DB::queryAndCountAffected($query);


		if( isset($this->policyIDs) && is_array($this->policyIDs) )
		{
			$policyIDs = $this->policyIDs;

			if( !USER_IS_ADMIN )
				$policyIDs = array_intersect($policyIDs, $allowedPolicyIDs);

			$query = DB::prepareQuery("INSERT INTO Asenine_UserGroupPolicies (userGroupID, policyID) SELECT %u, ID FROM Asenine_Policies WHERE ID IN %a", $this->userGroupID, $policyIDs);
			DB::queryAndGetID($query);
		}

		Message::addNotice(_('Rättigheter uppdaterade'));
	}

	public function saveUsers()
	{
		global $User;
		ensurePolicies('AllowEditUserGroup');

		$this->ensureExistence();

		$this->importArgs('userIDs');

		$query = DB::prepareQuery("DELETE FROM Asenine_UserGroupUsers WHERE userGroupID = %u", $this->userGroupID);
		DB::queryAndCountAffected($query);

		if( isset($this->userIDs) && is_array($this->userIDs) )
		{
			#$userGroupIDs = $this->userGroupIDs;

			$query = DB::prepareQuery("INSERT INTO Asenine_UserGroupUsers (userGroupID, userID) SELECT %u, ID FROM Users WHERE ID IN %a", $this->userGroupID, $this->userIDs);
			DB::queryAndGetID($query);
		}

		Message::addNotice(_('Användare uppdaterade'));
	}

	public function loadUserGroup()
	{
		global $User, $result;
		ensurePolicies('AllowViewUserGroup');
		$result = Dataset::getProperties($this->userGroupID);
	}

	public function deleteUserGroup()
	{
		global $User;
		ensurePolicies('AllowDeleteUserGroup');

		$this->ensureExistence();

		$query = DB::prepareQuery("DELETE FROM Asenine_UserGroups WHERE ID = %u", $this->userGroupID);
		DB::queryAndCountAffected($query);

		Message::addNotice(_('Grupp borttagen'));
	}
}

$AjaxIO = new UserGroupIO($action, array('userGroupID'));
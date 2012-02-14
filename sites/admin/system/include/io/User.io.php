<?
class UserIO extends AjaxIO
{
	private function ensureExistence()
	{
		if( !\Manager\Dataset\User::isExisting($this->userID) )
			throw New Exception(sprintf(_('Användar ID %u existerar inte'), $this->userID));
	}

	public function saveGeneral()
	{
		global $User;
		ensurePolicies('AllowEditUser');

		$this->ensureExistence();

		$this->importArgs(
			'isEnabled',
			'isAdministrator',
			'username',
			'fullname',
			'phone',
			'email',
			'newPassword',
			'timeAutoLogout');

		$this->userID = (int)$this->userID;
		$this->isEnabled = (bool)$this->isEnabled;
		$this->isAdministrator = (bool)$this->isAdministrator;
		$this->timeAutoLogout = abs($this->timeAutoLogout);

		$EditedUser = \User::loadOneFromDB($this->userID);

		if( strlen($this->username) > 0 )
			\Operation\User::verifyUsername($this->username, $this->userID);

		if( $EditedUser->isAdministrator() && $EditedUser->isEnabled !== $this->isEnabled && !USER_IS_ADMIN )
			Message::addAlert(_('Endast administratörer kan ändra aktivitet för administratörskonton'));

		if( $this->isAdministrator !== $EditedUser->isAdministrator() && !USER_IS_ADMIN )
			Message::addAlert(_('Endast administratörer kan växla administratorstatus'));

		if( !$this->isAdministrator && $this->userID === USER_ID && USER_IS_ADMIN )
		{
			$this->isAdministrator = true;
			Message::addAlert(_('Du kan ej ta bort din egen administratorstatus'));
		}

		if( !$this->isEnabled && $this->userID === USER_ID )
		{
			$this->isEnabled = true;
			Message::addAlert(_('Du kan ej avaktivera den inloggade användaren'));
		}

		$query = \DB::prepareQuery("UPDATE
				Users
			SET
				isEnabled = IF(%u, %u, isAdministrator),
				isAdministrator = IF(%u, %u, isAdministrator),
				username = NULLIF(%s, ''),
				fullname = NULLIF(%s, ''),
				email = NULLIF(%s, ''),
				phone = NULLIF(%s, ''),
				timeAutoLogout = NULLIF(%u, 0),
				timeModified = UNIX_TIMESTAMP()
			WHERE
				ID = %u",
			USER_IS_ADMIN, $this->isEnabled,
			USER_IS_ADMIN, $this->isAdministrator, ### Only Administrators can edit others Administrator status
			$this->username,
			$this->fullname,
			$this->email,
			$this->phone,
			$this->timeAutoLogout,
			$this->userID);

		if( !\DB::queryAndCountAffected($query) )
			throw New Exception(ERROR_DB_GENERAL);

		Message::addNotice(_('Användaruppgifter sparade'));

		if( strlen($this->newPassword) > 0 )
		{
			if( !$User->isAdministrator() )
				throw New Exception(_('Endast administratörer kan uppdatera lösenord'));

			if( strlen($this->newPassword) < \User::PASSWORD_MIN_LEN )
				throw New Exception(sprintf(_('Nytt lösenord för kort. Lösenord måste bestå av minst %u tecken.'), \User::PASSWORD_MIN_LEN));

			if( !\Manager\User::setPassword($this->userID, $this->newPassword) )
				throw New Exception(_('Lösenord kunde inte uppdateras'));

			Message::addNotice(_('Lösenord uppdaterat'));
		}

		$this->loadUser();
	}

	public function savePolicies()
	{
		global $User;
		ensurePolicies('AllowEditUser');

		$this->ensureExistence();

		$this->importArgs('policyIDs');

		### Only delete policies that current user has power over or all if is Administrator
		$allowedPolicyIDs = \Manager\Dataset\User::getPolicies(USER_ID);

		$query = \DB::prepareQuery("DELETE FROM UserPolicies WHERE userID = %u", $this->userID);
		if( !USER_IS_ADMIN )
			$query .= \DB::prepareQuery(" AND policyID IN %a", $allowedPolicyIDs);
		\DB::queryAndCountAffected($query);


		if( isset($this->policyIDs) && is_array($this->policyIDs) )
		{
			$policyIDs = $this->policyIDs;

			if( !USER_IS_ADMIN )
				$policyIDs = array_intersect($policyIDs, $allowedPolicyIDs);

			$query = \DB::prepareQuery("INSERT INTO UserPolicies (userID, policyID) SELECT %u, ID FROM Policies WHERE ID IN %a", $this->userID, $policyIDs);
			\DB::queryAndGetID($query);
		}

		Message::addNotice(_('Rättigheter uppdaterade'));
	}

	public function saveProfile()
	{
		global $User;

		$this->importArgs('email', 'fullname', 'phone');

		$query = \DB::prepareQuery("UPDATE
				Users
			SET
				fullname = %s,
				email = %s,
				phone = %s
			WHERE
				ID = %u",
			$this->fullname,
			$this->email,
			$this->phone,
			$User->userID);

		\DB::queryAndCountAffected($query);

		Message::addNotice(_('Profil uppdaterad'));
	}

	public function saveUserGroups()
	{
		global $User;
		ensurePolicies('AllowEditUser', 'AllowEditUserGroup');

		$this->ensureExistence();

		$this->importArgs('userGroupIDs');

		### Only delete policies that current user has power over
		$allowedUserGroupIDs = \Manager\Dataset\User::getGroups(USER_ID);

		$query = \DB::prepareQuery("DELETE FROM UserGroupUsers WHERE userID = %u", $this->userID);
		if( !USER_IS_ADMIN ) $query .= \DB::prepareQuery(" AND userGroupID IN %a", $allowedUserGroupIDs);
		\DB::queryAndCountAffected($query);

		if( isset($this->userGroupIDs) && is_array($this->userGroupIDs) )
		{
			$userGroupIDs = $this->userGroupIDs;

			if( !USER_IS_ADMIN )
				$userGroupIDs = array_intersect($userGroupIDs, $allowedUserGroupIDs);

			$query = \DB::prepareQuery("INSERT INTO UserGroupUsers (userID, userGroupID) SELECT %u, ID FROM UserGroups WHERE ID IN %a", $this->userID, $userGroupIDs);
			\DB::queryAndCountAffected($query);
		}

		Message::addNotice(_('Grupper uppdaterade'));
	}

	public function setPassword()
	{
		global $User;

		$this->ensureExistence();

		$this->importArgs('passwordCurrent', 'passwordNew', 'passwordNewVerify');

		\Operation\User::setPasswordAsUser($User->userID, $this->passwordCurrent, $this->passwordNew, $this->passwordNewVerify);

		Message::addNotice(_('Lösenord uppdaterat'));
	}

	public function loadUser()
	{
		global $User, $result;
		ensurePolicies('AllowViewUser');
		$result = \Manager\Dataset\User::getProperties($this->userID);
		$result['newPassword'] = '';
	}

	public function deleteUser()
	{
		global $User;
		ensurePolicies('AllowDeleteUser');

		$this->ensureExistence();

		if( (int)$this->userID === USER_ID ) throw New Exception(_('Kan ej ta bort den aktuella användaren'));

		if( \Manager\Dataset\User::isAdministrator($userID) && !USER_IS_ADMIN )
			throw New Exception(_('Du måste vara administratör för att ta bort en annan administratör'));

		$query = \DB::prepareQuery("DELETE FROM Users WHERE ID = %u", $this->userID);
		\DB::queryAndCountAffected($query);

		Message::addNotice(_('Användare borttagen'));
	}

}

$AjaxIO = new UserIO($action, array('userID'));
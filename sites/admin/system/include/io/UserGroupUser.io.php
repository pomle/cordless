<?	
ensurePolicies('AllowEditUserGroup', 'AllowEditUser');

interport('action', 'userGroupID', 'userID', 'username', 'usergroup', 'userGroupIDs', 'userIDs');

switch($action) {
	case 'save':
		if( $userID ) {
			$deleteQuery = DB::prepareQuery("DELETE FROM UserGroupUsers WHERE userID = %d", $userID);
			DB::queryAndCountAffected($deleteQuery); // Clean out old policies

			if( is_array($userGroupIDs) ) { 
				$insertQuery = DB::prepareQuery("INSERT INTO UserGroupUsers (userID, userGroupID) SELECT %d, ug.ID FROM UserGroups ug WHERE ug.ID IN (%s)", $userID, join(',', $userGroupIDs));
				DB::queryAndGetID($insertQuery); 
			}

			Message::addNotice(_('Användargrupper ändrade'));
		}

		if( $userGroupID ) {
			$deleteQuery = DB::prepareQuery("DELETE FROM UserGroupUsers WHERE userGroupID = %d", $userGroupID);
			DB::queryAndCountAffected($deleteQuery); // Clean out old policies

			if( is_array($userIDs) ) { 
				$insertQuery = DB::prepareQuery("INSERT INTO UserGroupUsers (userID, userGroupID) SELECT u.ID, %d FROM Users u WHERE u.ID IN (%s)", $userGroupID, join(',', $userIDs));
				DB::queryAndGetID($insertQuery); 
			}

			Message::addNotice(_('Användare ändrade'));
		}
		
		break;

	case 'add':
		ensurePolicies('AllowAddGroupUser');

		if(!$userGroupID) {
			if( !strlen($usergroup) ) throw New Exception(_('Inget gruppnamn angivet'));
			if( !($userGroupID = DB::queryAndFetchOne(DB::prepareQuery("SELECT ID FROM UserGroups WHERE name = %s", $usergroup))) ) throw New Exception(sprintf(_('Grupp ogitlig: "%s"'), $usergroup));
		}

		if(!$userID) {
			if( !strlen($username) ) throw New Exception(_('Inget användarnamn angivet'));
			if( !($userID = DB::queryAndFetchOne(DB::prepareQuery("SELECT ID FROM Users WHERE username = %s", $username))) ) throw New Exception(sprintf(_('Användare ogitlig: "%s"'), $username));
		}

		if(!$user->isAdministrator() && !(bool)DB::queryAndFetchOne(DB::prepareQuery("SELECT COUNT(*) FROM UserGroups ug JOIN UserGroupUsers ugu ON ugu.userGroupID = ug.ID WHERE ugu.userID = %d AND ug.ID = %d", CURRENT_USER_ID, $userGroupID))) throw New Exception(_('Användare kan endast lägga till användare i grupper de själva är medlemmar av'));

		if($userID && $userGroupID) {
			$query = DB::prepareQuery("REPLACE INTO UserGroupUsers (userGroupID, userID) VALUES(%d, %d)", $userGroupID, $userID);
			DB::queryAndGetID($query);
			Message::addNotice(MESSAGE_ROW_UPDATED);
			$result = array('userGroupID' => 0, 'userID' => 0, 'username' => '', 'groupname' => '');
		}else{
			throw New Exception('Missing IDs');
		}

		Message::addCall('reloadListing("#groupsNew");');
		break;

	case 'load':
		$query = DB::prepareQuery("SELECT u.ID AS userID, ug.ID AS userGroupID, ug.name AS usergroup, u.username AS username FROM UserGroupUsers ugu JOIN Users u ON u.ID = ugu.userID JOIN UserGroups ug ON ug.ID = ugu.userGroupID WHERE ugu.userID = %d OR ugu.userGroupID = %d", $userID, $userGroupID);
		$result = DB::assoc(DB::queryAndFetchResult($query));
		break;

	case 'remove':
		ensurePolicies('AllowRemoveGroupUser');
		$query = DB::prepareQuery("DELETE FROM UserGroupUsers WHERE userID = %d AND userGroupID = %d", $userID, $userGroupID);
		if(DB::queryAndCountAffected($query)) Message::addNotice(MESSAGE_ROW_DELETED);
		Message::addCall('reloadListing("#groupsNew");');
		break;		

	case 'new':
		break;
}
<?
interport('action', 'userGroupID', 'policyID', 'policy', 'policyIDs');

switch($action) {
	case 'save':
		ensurePolicies('AllowEditUserGroup');
		
		if( $userGroupID ) {
			$deleteQuery = DB::prepareQuery("DELETE FROM UserGroupPolicies WHERE userGroupID = %d", $userGroupID);
			DB::queryAndCountAffected($deleteQuery); // Clean out old policies

			if( is_array($policyIDs) ) { 
				$insertQuery = DB::prepareQuery("INSERT INTO UserGroupPolicies (policyID, userGroupID) SELECT p.ID, %d FROM Policies p WHERE p.ID IN (%s)", $userGroupID, join(',', $policyIDs));
				DB::queryAndGetID($insertQuery); 
			}

			Message::addNotice(_('Rättigheter ändrade'));
		}

		break;

	case 'add':
		ensurePolicies('AllowEditUserGroup', 'AllowSetPolicy');
		if(!strlen($policy)) throw New Exception(_('Ingen rättighet angiven'));
		if($policyID = DB::pick(DB::prepareQuery("SELECT ID FROM Policies WHERE policy = %s", $policy))) {
			$query = DB::prepareQuery("REPLACE INTO UserGroupPolicies (userGroupID, policyID) SELECT %d, ID FROM Policies WHERE ID = %d", $userGroupID, $policyID);
			DB::queryAndGetID($query);
			message::addNotice(MESSAGE_ROW_UPDATED);
		}else{
			throw New Exception(sprintf(_('Rättighet ogitlig: "%s"'), $policy));
		}

		$result = array('policyID' => 0, 'policy' => '');
		break;

	case 'load':
		$query = DB::prepareQuery("SELECT ID AS policyID, policy FROM Policies WHERE ID = %d", $policyID);
		$result = DB::assoc(DB::queryAndFetchResult($query));
		break;

	case 'remove':
		ensurePolicies('AllowEditUserGroup', 'AllowSetPolicy');
		$query = DB::prepareQuery("DELETE FROM UserGroupPolicies WHERE policyID = %d AND userGroupID = %d", $policyID, $userGroupID);
		if(DB::queryAndCountAffected($query)) message::addNotice(MESSAGE_ROW_DELETED);
		break;

	case 'new':
		break;
}
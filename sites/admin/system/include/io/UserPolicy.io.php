<?
ensurePolicies('AllowEditUser','AllowSetPolicy');

use \Asenine\DB;

interport('action', 'userID', 'policyID', 'policy', 'policyIDs');

switch($action)
{
	case 'save':
		try
		{
			DB::autocommit(false);

			if( $user->isAdministrator() ) {

				$deleteQuery = DB::prepareQuery("DELETE FROM Asenine_UserPolicies WHERE userID = %d", $userID);
				$insertQuery = DB::prepareQuery("INSERT INTO Asenine_UserPolicies (userID, policyID) SELECT %d, p.ID FROM Asenine_Policies p WHERE p.ID IN (%s)", $userID, $policyIDs);

			}else{

				$currentUserPolicyIDs = $user->getPolicies();

				$deleteQuery = DB::prepareQuery("DELETE FROM Asenine_UserPolicies WHERE userID = %d AND policyID IN (%s)", $userID, join(',', $currentUserPolicyIDs));
				$insertQuery = DB::prepareQuery("INSERT IGNORE INTO Asenine_UserPolicies (userID, policyID) SELECT upd.userID, p.ID FROM Asenine_Policies p JOIN Asenine_UserPolicies upd ON upd.policyID = p.ID JOIN Asenine_UserPolicies ups ON ups.userID = %d AND ups.policyID = upd.policyID WHERE upd.userID = %d AND p.ID IN (%s)", $user->userID, $userID, $policyIDs);
			}

			DB::queryAndCountAffected($deleteQuery); // Clean out old policies
			if( is_array($policyIDs) ) { DB::queryAndGetID($insertQuery); } // If no policyIDs is sent, don't bother to put any back

			DB::commit();

			Message::addNotice(_('Rättigheter ändrade'));
			Message::addCall('reloadListing("#policies");');

		}
		catch(Exception $e)
		{
			DB::rollback();

			throw New Exception($e->getMessage());
		}
	break;

	case 'add':
		if( strlen($policy) == 0 )
			throw new Exception(_('Ingen rättighet angiven'));

		if( $policyID = DB::queryAndFetchOne( DB::prepareQuery("SELECT ID FROM Asenine_Policies WHERE policy = %s", $policy)) )
		{
			if( !$user->isAdministrator() && !$user->getPolicy($policy) ) throw New Exception(_('Användare kan endast lägga till rättigheter de själva besitter'));

			$query = DB::prepareQuery("REPLACE INTO Asenine_UserPolicies (userID, policyID) SELECT %d, ID FROM Asenine_Policies WHERE ID = %d", $userID, $policyID);
			DB::queryAndGetID($query);

			Message::addNotice(MESSAGE_ROW_UPDATED);
		}
		else
		{
			throw New Exception(sprintf(_('Rättighet ogitlig: "%s"'), $policy));
		}

		$result = array('policyID' => 0, 'policy' => '');
		Message::addCall('reloadListing("#policiesNew");');
	break;

	case 'load':
		$query = DB::prepareQuery("SELECT ID AS policyID, policy FROM Asenine_Policies WHERE ID = %d", $policyID);
		$result = DB::assoc(DB::queryAndFetchResult($query));
	break;

	case 'remove':
		$query = DB::prepareQuery("DELETE FROM Asenine_UserPolicies WHERE policyID = %d AND userID = %d", $policyID, $userID);
		if(DB::queryAndCountAffected($query)) Message::addNotice(MESSAGE_ROW_DELETED);
		Message::addCall('reloadListing("#policiesNew");');
	break;
}
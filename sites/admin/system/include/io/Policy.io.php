<?
interport('action', 'policyID');

switch($action) {
	case 'save':
		ensurePolicies('AllowEditPolicy');

		interport('policy', 'description');

		if(!preg_match('%^[A-Za-z]+$%', $policy)) throw New Exception(_('Ogiltigt namn på rättighet. Endast A-Z och a-z ät tillåtet.'));

		if( !$policyID)
		{
			ensurePolicies('AllowCreatePolicy');
			$query = DB::prepareQuery("INSERT INTO Policies (policy, description) VALUES(%s, %s)", $policy, $description);
			$policyID = DB::queryAndGetID($query);
			Message::addNotice(MESSAGE_ROW_CREATED);
		}
		else
		{
			ensurePolicies('AllowEditPolicy');
			$query = DB::prepareQuery("UPDATE Policies SET policy = %s, description = %s WHERE ID = %d", $policy, $description, $policyID);
			DB::queryAndCountAffected($query);
			Message::addNotice(MESSAGE_ROW_UPDATED);
		}

	case 'load':
		ensurePolicies('AllowViewPolicy');
		$query = DB::prepareQuery("SELECT ID as policyID, policy, description FROM Policies WHERE ID = %d", $policyID);
		$result = DB::queryAndFetchOne($query);
		break;

	case 'delete':
		ensurePolicies('AllowDeletePolicy');
		if(!DB::queryAndFetchOne(DB::prepareQuery("SELECT COUNT(*) FROM Policies WHERE ID = %d", $policyID))) throw New Exception(MESSAGE_ROW_MISSING);
		$query = DB::prepareQuery("DELETE FROM Policies WHERE ID = %d", $policyID);
		DB::queryAndCountAffected($query);
		Message::addNotice(MESSAGE_ROW_DELETED);
		break;
}
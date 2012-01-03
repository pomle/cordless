<?
interport('securityBlockedIPID', 'hostAddress', 'isEnabled', 'comment');

function activate()
{
	cachePurge('SecurityBlockedIPsUpdate');
}

switch($action) {
	case 'activate':
		ensurePolicies('AllowActivateSecurityBlockedIP');
		activate();
		break;

	case 'save':
		ensurePolicies('AllowEditSecurityBlockedIP');
		$ipAsLong = ip2long($hostAddress);
		if( $ipAsLong === false ) throw New Exception('Ogiltig IP-adress angiven');

		$query = DB::prepareQuery("REPLACE INTO SecurityBlockedIPs (timeCreated, isEnabled, hostAddress, comment) VALUES(UNIX_TIMESTAMP(), %d, %u, %s)", $isEnabled, $ipAsLong, $comment);
		DB::queryAndCountAffected($query);
		Message::addNotice(MESSAGE_ROW_UPDATED);
		activate();

	case 'load':
		ensurePolicies('AllowViewSecurityBlockedIP');
		$query = DB::prepareQuery("SELECT sbip.ID AS securityBlockedIPID, sbip.timeCreated, sbip.isEnabled, INET_NTOA(sbip.hostAddress) AS hostAddress, sbip.comment FROM SecurityBlockedIPs sbip WHERE sbip.ID = %d", $securityBlockedIPID);
		$result = DB::pick($query);
		break;

	case 'delete':
		ensurePolicies('AllowEditSecurityBlockedIP');
		$query = DB::prepareQuery("DELETE FROM SecurityBlockedIPs WHERE ID = %d", $securityBlockedIPID);
		DB::queryAndCountAffected($query);
		activate();
		Message::addNotice(MESSAGE_ROW_DELETED);
		break;

	case 'new':
		$result['isEnabled'] = true;
		break;
}


<?
namespace Element\Antiloop;

require DIR_ANTILOOP_LISTS . 'UserSecurityIPs.inc.php';

$Antiloop->addFilter(
	new Filter\Hidden('userID')
);

$Antiloop->dropField('username');

$Stmt->addWhere('usip.userID = %u', $filter['userID']);
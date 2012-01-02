<?
namespace Element\Antiloop;

require DIR_ANTILOOP_LISTS . 'SecurityBlockedIPs.inc.php';

$Antiloop->addField(Field::ajaxLoad('SecurityBlockedIP', array('securityBlockedIPID')));
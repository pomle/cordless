<?
namespace Element\Antiloop;

require DIR_ANTILOOP_LISTS . 'UserSecurityIPs.User.inc.php';

$Antiloop->addField(Field::ajaxLoad('UserSecurityIP', array('userSecurityIPID')));
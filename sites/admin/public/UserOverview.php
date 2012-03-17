<?
#MENUPATH:System/Users
define('ACCESS_POLICY', 'AllowViewUser');

require '../Init.inc.php';

$pageTitle = _('System');
$pageSubtitle = _('Users');

$List = \Element\Antiloop::getAsDomObject(USER_IS_ADMIN ? 'Users.Administrator' : 'Users');

require HEADER;

echo $List;

require FOOTER;
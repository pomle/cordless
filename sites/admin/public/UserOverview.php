<?
#MENUPATH:System/Användare
define('ACCESS_POLICY', 'AllowViewUser');

require '../Init.inc.php';

$pageTitle = _('System');
$pageSubtitle = _('Användare');

$List = \Element\Antiloop::getAsDomObject(USER_IS_ADMIN ? 'Users.Administrator' : 'Users');

require HEADER;

echo $List;

require FOOTER;
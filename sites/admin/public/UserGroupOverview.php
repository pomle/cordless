<?
#MENUPATH:System/Användargrupper
define('ACCESS_POLICY', 'AllowViewUserGroup');

require '../Init.inc.php';

$pageTitle = _('System');
$pageSubtitle = _('Användargrupper');

$List = \Element\Antiloop::getAsDomObject('UserGroups');

require HEADER;

echo $List;

require FOOTER;
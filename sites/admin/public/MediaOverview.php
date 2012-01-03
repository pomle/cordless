<?
#MENUPATH:Databas/Media
define('ACCESS_POLICY', 'AllowViewMedia');

require '../Init.inc.php';

$pageTitle = _('Media');
#$pageSubtitle = _('Media');

$MediaList = \Element\Antiloop::getAsDomObject('Media');

require HEADER;

echo $MediaList;

require FOOTER;
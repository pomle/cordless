<?
#MENUPATH:System/Diagnostics
define('ACCESS_POLICY', 'AllowViewDiagnostics');

require_once '../Init.inc.php';

$pageTitle = _('Diagnostics');

$Menu = new \Element\TextMenu();
$Menu
	->addItem('/DiagnosticsCacheView.php', 'Cache');

require HEADER;

echo $Menu;

require FOOTER;
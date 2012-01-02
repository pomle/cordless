<?
function displayMenu()
{
	global $User;

	$treeInclude = DIR_ADMIN_CONFIG . 'MenuTree.inc.php';
	if( !file_exists($treeInclude) ) return false;

	include $treeInclude;

	if( !isset($tree) ) return false;

	$Menu = new \Element\MainMenu($tree);

	if( !$User->isAdministrator() )
		$Menu->filterPolicies($User->policies);

	echo $Menu;
}
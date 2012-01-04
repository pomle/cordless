<?
function displayMenu()
{
	global $User;

	$treeInclude = DIR_ADMIN_CONFIG . 'MenuTree.inc.php';
	if( !file_exists($treeInclude) ) return false;

	include $treeInclude;
	if( !isset($tree) ) return false;

	$orderInclude = DIR_ADMIN_CONFIG . 'MenuOrder.inc.php';
	if( file_exists($orderInclude) )
	{
		include $orderInclude;
		if( isset($order) )
		{
			$sortedTree = array();
			foreach($order as $treeName)
			{
				if( isset($tree[$treeName]) )
				{
					$sortedTree[$treeName] = $tree[$treeName];
					unset($tree[$treeName]);
				}
			}

			$tree = array_merge($sortedTree, $tree);
		}
	}

	$Menu = new \Element\MainMenu($tree);

	if( !$User->isAdministrator() )
		$Menu->filterPolicies($User->policies);

	echo $Menu;
}
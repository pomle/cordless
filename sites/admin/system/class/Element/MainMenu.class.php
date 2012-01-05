<?
namespace Element;

class MainMenu extends Common\Root
{
	public function __construct(Array $tree = array())
	{
		$this->tree = $tree;
		$this->policies = array();
	}

	public function __toString()
	{
		ob_start();
		$this->printBranch($this->tree);
		return ob_get_clean();
	}


	public function addItem($path, $href, $policy)
	{
		$levels = explode('/', $path);

		$item = array(
			'caption' => array_pop($levels),
			'href' => $href,
			'policy' => $policy
		);

		$items = &$this->tree;

		while($level = array_shift($levels))
		{
			if( !isset($items[$level]) )
				$items[$level] = array();

			$items = &$items[$level];
		}

		$items[] = $item;

		return $this;
	}

	public function filterBranch(Array &$branch, $policies)
	{
		### Our default position is that we are going to remove this whole $branch from the tree
		$hasItemsLeft = false;

		foreach($branch as $key => &$item)
		{
			### A numeric key means this is an actual item
			if( is_numeric($key) )
			{
				### Item shouldn't be accessible based on the current policy set, so remove it
				if( isset($item['policy']) && !in_array($item['policy'], $policies) )
				{
					#printf("Removing \"%s\"\n", $item['caption']);
					unset($branch[$key]);
				}
				### Report to back that there are items in here that should be displayed, so branch has to be kept
				else
				{
					$hasItemsLeft = true;
				}
			}
			else
			{
				### We are on a branch, and delving deeper, and if there are items in here, this current branch should also be kept
				#printf("Delving into \"%s\"\n", $key);
				if( ($hasItemsLeft = $this->filterBranch($item, $policies)) === false )
					unset($branch[$key]);
			}
		}

		#printf("Returning: %u\n", $hasItemsLeft);

		return $hasItemsLeft;
	}

	public function filterPolicies($policies)
	{
		$this->filterBranch($this->tree, $policies);
		return true;
	}

	public function getAsVariable()
	{
		$var = var_export($this->tree, true);
		$var = preg_replace("/([\"\'].+[\'\"]) => \n/", "_(\\1) => \n", $var); ### Apply gettext
		$var = preg_replace("/'caption' => (.+),/", "'caption' => _(\\1),", $var); ### Apply gettext
		return $var;
	}

	public function printBranch(Array $branch, $title = null)
	{
		if( count($branch) > 0 )
		{
			if( $title ) printf('%s', htmlspecialchars($title));

			echo "<ul>";

			foreach($branch as $key => $item)
			{
				echo '<li>';

				if( is_numeric($key) )
					printf('<a href="%s">%s</a>', $item['href'], $item['caption']);
				else
					$this->printBranch($item, $key);

				echo '</li>';
			}

			echo "</ul>";
		}
	}
}
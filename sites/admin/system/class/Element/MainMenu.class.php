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
		$hasItemsLeft = false;

		foreach($branch as $key => &$item)
		{
			if( is_numeric($key) )
			{
				if( isset($item['policy']) && !in_array($item['policy'], $policies) )
					unset($branch[$key]);
				else
					$hasItemsLeft = true;
			}
			else
			{
				if( $this->filterBranch($item, $policies) )
					$hasItemsLeft = true;
			}
		}

		return $hasItemsLeft;
	}

	public function filterPolicies($policies)
	{
		$this->filterBranch($this->tree, $policies);
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
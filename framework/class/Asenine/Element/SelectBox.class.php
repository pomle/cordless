<?
namespace Asenine\Element;

class SelectBox extends Common\Root
{
	public
		$name,
		$selectedKey,
		$items;


	public static function keyValue($name, $selectedKey, Array $values)
	{
		$SelectBox = new self($name, $selectedKey);
		$SelectBox->addItemsFromArray($values, true);
		return $SelectBox;
	}

	public static function keyPair($name, $selectedKey, Array $values)
	{
		$SelectBox = new self($name, $selectedKey);
		$SelectBox->addItemsFromArray($values, false);
		return $SelectBox;
	}



	public function __construct($name = null, $selectedKey = null, Array $items = array())
	{
		$this->name = $name;
		$this->selectedKey = $selectedKey;
		$this->items = $items;
	}

	public function __toString()
	{
		ob_start();
		?><select name="<? echo htmlspecialchars($this->name); ?>"<? echo $this->getAttributes(); ?>><?
		foreach($this->items as $key => $value)
		{
			$isSelected = ($this->selectedKey && $this->selectedKey == $key);
			?><option value="<? echo htmlspecialchars($key); ?>" <? if($isSelected) echo 'selected="selected"'; ?>><? echo htmlspecialchars($value); ?></option><?
		}
		?></select><?
		return ob_get_clean();
	}


	public function addItemsFromArray(Array $array, $useValueAsKey = false)
	{
		foreach($array as $key => $value)
		{
			$this->addItem($value, $useValueAsKey ? $value : $key);
		}
	}

	public function addItemsFromArrayOfArrays(Array $arrays, $valueName, $keyName = null)
	{
		foreach($arrays as $arrayKey => $array)
		{
			$this->addItem($array[$valueName], isset($keyName) ? $array[$keyName] : $arrayKey);
		}
	}

	public function addItemsFromArrayOfObjects(Array $objects, $valueName, $keyName = null)
	{
		if( !is_string($valueName) ) trigger_error(__METHOD__ . ' requires parameter 2 to be string', E_USER_ERROR);

		foreach($objects as $objectKey => $Object)
		{
			$this->addItem
			(
				$Object->$valueName,
				isset($keyName) ? $Object->$keyName : $objectKey
			);
		}
	}

	public function addItem($value, $key = null)
	{
		if( is_null($key) )
			$this->items[$value] = $value;
		else
			$this->items[$key] = $value;

		return $this;
	}

	public function sort()
	{
		return $this->sortValuesAlpha();
	}

	public function sortValuesAlpha()
	{
		asort($this->items, SORT_LOCALE_STRING);
		return $this;
	}
}
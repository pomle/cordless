<?
namespace Element\Antiloop\Filter;

class Select extends Common\Root
{
	public static function fromArray($name, $icon = null, $values, $isBlankSelectable = false)
	{
		$S = new self($name, $icon, $isBlankSelectable);
		foreach($values as $key => $value)
			$S->addOption($value, $key);
		return $S;
	}


	public function __construct($name, $icon = null, $isBlankSelectable = false)
	{
		$this->icon = $icon;
		$this->name = $name;
		$this->caption = null;
		$this->options = array();
		if( $isBlankSelectable ) $this->addOption('[' . _('Samtliga') . ']', '0');
		$this->selectedKey = null;
	}

	public function __toString()
	{
		return
			(string)\Element\Icon::custom($this->icon, $this->caption) .
			(string)new \Asenine\Element\SelectBox('filter[' . $this->name . ']', $this->selectedKey, $this->options);
	}


	public function addOption($value, $key = null)
	{
		$key = (string)(is_null($key) ? $value : $key);
		$this->options[$key] = (string)$value;
		return $this;
	}


	public function importParams(array $params)
	{
		$this->selectedKey = $params[$this->name];
	}
}
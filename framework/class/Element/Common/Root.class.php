<?
namespace Element\Common;

interface _Root
{
	public function __toString();
}

abstract class Root implements _Root
{
	public function addAttr($key, $value)
	{
		$this->attributes[$key][] = $value;
		return $this;
	}

	public function addClass($class)
	{
		return $this->addAttr('class', $class);
	}

	public function addClasses(Array $classes)
	{
		foreach($classes as $class)
			$this->addClass($class);
		return $this;
	}

	public function addData($prefix, $content)
	{
		return $this->addAttr('data-' . $prefix, $content);
	}

	public function addID($ID)
	{
		return $this->addAttr('id', $ID);
	}

	public function getAttributes()
	{
		$string = '';

		if( isset($this->attributes) )
			foreach($this->attributes as $attribute => $values)
				$string .= sprintf(' %s="%s"', htmlspecialchars($attribute), htmlspecialchars(join(' ', $values)));

		return $string;
	}
}
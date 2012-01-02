<?
namespace Element\Antiloop\Filter;

class Hidden extends Common\Root
{
	public function __construct($name, $value = null)
	{
		$this->name = $name;
		$this->value = $value;
	}

	public function __toString()
	{
		return (string)\Element\Input::hidden(sprintf('filter[%s]', $this->name), $this->value);
	}


	public function importParams(Array $params)
	{
		$this->value = isset($params[$this->name]) ? $params[$this->name] : null;
	}
}
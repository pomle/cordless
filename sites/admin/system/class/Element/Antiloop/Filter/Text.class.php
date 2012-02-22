<?
namespace Element\Antiloop\Filter;

class Text extends Common\Root
{
	public function __construct($name, $icon, $caption)
	{
		$this->icon = $icon;
		$this->name = $name;
		$this->caption = $caption;
		$this->classes = array('text');
		$this->currentString = '';
	}

	public function __toString()
	{
		return
			(string)\Element\Icon::custom($this->icon, $this->caption) .
			(string)\Element\Input::text('filter[' . $this->name . ']', $this->currentString)->size($this->size)->addClasses($this->classes) .
			'<a href="#" class="clear pD">' . (string)\Element\Icon::custom('textfield_delete', _('Rensa fält')) . '</a>';
	}


	public function importParams(array $params)
	{
		$this->currentString = isset($params[$this->name]) ? $params[$this->name] : '';
	}
}
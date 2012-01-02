<?
namespace Element;

class ToolBar extends Common\Root
{
	public $icon, $caption;
	public $tools = array();

	public function __construct($icon, $caption = '')
	{
		$this->icon = $icon;
		$this->caption = $caption;
		$this->addClass('toolbar');
	}

	public function __toString()
	{
		$html = '<div' . $this->getAttributes() . '>';
		if($this->icon) $html.= Icon::custom($this->icon, $this->caption);

		foreach($this->tools as $Tool)
			$html .= (string)$Tool;

		$html.= '</div>';

		return $html;
	}


	public function addTools()
	{
		foreach(func_get_args() as $Tool)
			$this->addTool($Tool);

		return $this;
	}

	public function addTool(Tool $Tool)
	{
		$this->tools[] = $Tool;
		return $this;
	}
}

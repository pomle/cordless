<?
namespace Element;

global $js;
$js[] = '/js/jquery/jquery.selection.js';
$js[] = '/js/jquery/jquery.insertAtCaret.js';
$js[] = '/js/jquery/jquery.textInsert.js';
$js[] = '/js/plugins/TextEditor.plugin.js';
$js[] = '/js/Populus.js';

class TextEditor extends TextArea
{
	public function __construct($name, $content = null, $cols = null, $rows = null)
	{
		parent::__construct($name, $content, $cols, $rows);
		$this->toolbars = array();
	}

	public function __toString()
	{
		$html = '<div class="textEditor">';

		foreach($this->toolbars as $ToolBar)
			$html .= (string)$ToolBar;

		$html .= parent::__toString();
		$html .= '</div>';

		return $html;
	}


	public function addToolBar(ToolBar $ToolBar)
	{
		$this->toolbars[] = $ToolBar;
	}
}
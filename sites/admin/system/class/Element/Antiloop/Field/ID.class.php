<?
namespace Element\Antiloop\Field;

class ID extends Common\Root
{
	public function __construct($name, $caption, $icon)
	{
		parent::__construct($name, $caption, $icon);
		$this->class = 'number';
		$this->setContentHandler(
			function($value)
			{
				return sprintf('<b>%d</b>', $value);
			}
		);
	}
}
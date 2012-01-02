<?
namespace Element\Antiloop\Field;

class Text extends Common\Root
{
	public static function raw($name, $caption = null, $icon = null)
	{
		$Field = new self($name, $caption, $icon);
		$Field->isTextual = true;
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				return str_replace(' ', '&nbsp;', htmlspecialchars($value));
			}
		);
		return $Field;
	}
}
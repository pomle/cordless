<?
namespace Element\Antiloop\Field;

class IP extends Common\Root
{
	public static function v4DotNotation($name, $caption = null, $icon = null)
	{
		$Field = new self($name, $caption, $icon);
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				return long2ip($value);
			}
		);
		return $Field;
	}

	public static function raw($name, $caption = null, $icon = null)
	{
		$Field = new self($name, $caption, $icon);
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				return str_replace(' ', '&nbsp;', htmlspecialchars($value));
			}
		);
		return $Field;
	}
}
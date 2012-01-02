<?
namespace Element\Antiloop\Field;

class File extends Common\Root
{
	public static function name($name, $caption = null, $icon = null)
	{
		$Field = Text::raw($name, $caption ?: _('Filnamn'), $icon ?: 'folder_brick');
		return $Field;
	}

	public static function size($name, $caption = null, $icon = null)
	{
		$Field = new self($name, $caption ?: _('Storlek'), $icon ?: 'report_disk');
		$Field->class = 'number';
		$Field->setContentHandler(
			function($value)
			{
				return is_numeric($value) ? \Format::fileSize($value) : '-';
			}
		);
		return $Field;
	}
}
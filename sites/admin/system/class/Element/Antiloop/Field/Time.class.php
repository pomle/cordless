<?
namespace Element\Antiloop\Field;

class Time extends Common\Root
{
	public static function date($name, $caption = null, $icon = null)
	{
		$Field = new self($name, $caption ?: _('Datum'), $icon ?: 'calendar');
		$Field->isSortReversed = true;
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				return htmlspecialchars($value > 0 ? strftime(FORMAT_DATE, $value) : '-');
			}
		);
		return $Field;
	}

	public static function elapsed($name, $caption = null, $icon = null)
	{
		$Field = new self($name, $caption ?: _('Förfluten tid'), $icon ?: 'time_go');
		$Field->isSortReversed = true;
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				return htmlspecialchars($value > 0 ? strftime(FORMAT_DATE, $value) : '-');
			}
		);
		return $Field;
	}

	public static function stamp($name, $caption = null, $icon = null)
	{
		$Field = new self($name, $caption ?: _('Tid'), $icon ?: 'time');
		$Field->isSortReversed = true;
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				return htmlspecialchars($value > 0 ? strftime(FORMAT_DATETIME, $value) : '-');
			}
		);
		return $Field;
	}
}
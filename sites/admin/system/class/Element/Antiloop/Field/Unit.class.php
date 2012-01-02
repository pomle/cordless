<?
namespace Element\Antiloop\Field;

class Unit extends Number
{
	public static function millimeter($name, $caption = null, $icon = 'ruler')
	{
		return new self($name, $caption ?: _('Längd'), $icon, 0, 1000, ' mm');
	}

	public static function kilometer($name, $caption = null, $icon = 'ruler')
	{
		return new self($name, $caption ?: _('Längd'), $icon, 2, 1/1000, 'km');
	}

	public static function liter($name, $caption = null, $icon = 'ruler')
	{
		return new static($name, $caption ?: _('Volym'), $icon, 2, 1000, ' l');
	}


	public function __construct($name, $caption, $icon, $numDecimals = 2, $multiplier = 1, $suffix = '')
	{
		parent::__construct($name, $caption, $icon);
		$this->isSortReversed = true;
		$this->numDecimals = (int)$numDecimals;
		$this->multiplier = (float)$multiplier;
		$this->suffix = $suffix;
		$this->setContentHandler(
			function($value, $Field, $dataRow)
			{
				return sprintf('%.' . $Field->numDecimals . 'f%s', $value * $Field->multiplier, $Field->suffix);
			}
		);
	}
}
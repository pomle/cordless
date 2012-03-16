<?
namespace Element\Antiloop\Field;

class Number extends Common\Root
{
	public static function currency($name, $caption, $icon, $format)
	{
		$Field = new self($name, $caption, $icon);
		$Field->class = 'number';
		$Field->isSortReversed = true;
		$Field->format = $format;
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				return money_format($Field->format, $value);
			}
		);
		return $Field;
	}

	public static function decimal($name, $caption, $icon, $numDecimals = 2, $multiplier = 1)
	{
		$Field = new self($name, $caption, $icon);
		$Field->class[] = 'number';
		$Field->numDecimals = (int)$numDecimals;
		$Field->multiplier = (float)$multiplier;
		$Field->isSigned = false;
		$Field->isSortReversed = true;
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				if( !is_numeric($value) ) return '-';
				return sprintf('%.' . $Field->numDecimals . 'f', $value * $Field->multiplier);
			}
		);
		return $Field;
	}

	public static function integer($name, $caption, $icon)
	{
		$Field = new self($name, $caption, $icon);
		$Field->class[] = 'number';
		$Field->isSigned = false;
		$Field->isSortReversed = true;
		$Field->setContentHandler(
			function($value, $Field)
			{
				if( !is_numeric($value) ) return '-';
				return $Field->isSigned ? sprintf('%+d', $value) : sprintf('%d', $value);
			}
		);
		return $Field;
	}


	public function isSigned($bool)
	{
		$this->isSigned = (bool)$bool;
		return $this;
	}
}
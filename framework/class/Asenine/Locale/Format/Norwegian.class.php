<?
namespace Asenine\Locale\Format;

class Norwegian extends Common\Root
{
	public function number($value, $decimals = 2)
	{
		return number_format($value, $decimals, ',', ' ');
	}

	public function price($value, $useShort = false)
	{
		if( $useShort )
		{
			return round($value) == $value ? number_format($value, 0) . ':-' : number_format($value, 2, ':', ' ');
		}
		else
		{
			return parent::price($value, false);
		}
	}
}
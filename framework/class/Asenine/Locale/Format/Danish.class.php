<?
namespace Asenine\Locale\Format;

class Danish extends Common\Root
{
	const TIMESTAMP_FORMAT = '%c';
	const TIMESTAMP_FORMAT_SHORT = '%Y-%m-%d %H:%M';

	const DATE_FORMAT = '%d. %B %Y';
	const DATE_FORMAT_SHORT = '%d/%m/%Y';

	const TIME_FORMAT = '%H:%M';
	const TIME_FORMAT_SHORT = '%H:%M';

	public function number($value, $decimals = 2)
	{
		return number_format($value, $decimals, ',', '.');
	}

	public function price($value, $useShort = false)
	{
		if( $useShort )
		{
			return round($value) == $value ? number_format($value, 0) . ',-' : number_format($value, 2, ',', ' ');
		}
		else
		{
			return parent::price($value, false);
		}
	}
}
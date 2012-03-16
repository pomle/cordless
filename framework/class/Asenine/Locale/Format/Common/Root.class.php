<?
namespace Asenine\Locale\Format\Common;

class Root
{
	const PRICE_FORMAT = '%n';
	const PRICE_FORMAT_SHORT = '%!n';

	const CURRENCY_FORMAT = '%i';
	const CURRENCY_FORMAT_SHORT = '%n';

	const TIMESTAMP_FORMAT = '%c';
	const TIMESTAMP_FORMAT_SHORT = '%Y-%m-%d %H:%M';

	const DATE_FORMAT = '%Y-%m-%d';
	const DATE_FORMAT_SHORT = '%Y-%m-%d';

	const TIME_FORMAT = '%H:%M:%S';
	const TIME_FORMAT_SHORT = '%H:%M';

	public function currency($value, $useShort = false)
	{
		return $useShort ? money_format(static::CURRENCY_FORMAT_SHORT, $value) : money_format(static::CURRENCY_FORMAT, $value);
	}

	public function daysSince($seconds, $round = 1)
	{
		if (!is_numeric($round))
			$round = 1;

		$days = round( $seconds / (60*60*24), $round );
		return sprintf(_('%u dagar'), $days);
	}

	public function elapsedTime($seconds, $longMode = false)
	{
		$timeIntervals = array(
			'week' => array(
				'duration' => 60*60*24*7,
				'label' => _('%u veckor')
			),
			'day' => array(
				'duration' => 60*60*24,
				'label' => _('%u dagar')
			),
			'hour' => array(
				'duration' => 60*60,
				'label' => _('%u timmar')
			),
			'minute' => array(
				'duration' => 60,
				'label' => _('%u minuter')
			),
			'second' => array(
				'duration' => 1,
				'label' => _('%u sekunder')
			)
		);

		$steps = $longMode ? count($timeIntervals) : 2;

		foreach($timeIntervals as &$timeInterval)
		{
			$quantity = floor($seconds / $timeInterval['duration']);
			$timeInterval['quantity'] = $quantity;
			$seconds-= $timeInterval['quantity'] * $timeInterval['duration'];
		}
		unset($timeInterval);
		$sentences = array();

		$allowSkip = true;
		foreach($timeIntervals as $timeInterval)
		{

			if( $allowSkip && $timeInterval['quantity'] == 0 ) continue;

			$allowSkip = false;
			$sentences[] = sprintf($timeInterval['label'], $timeInterval['quantity']);

			if( count($sentences) >= $steps ) break;
		}

		return join(', ', $sentences);
	}

	public function fileSize($bytes)
	{
		if(is_numeric($bytes) && $bytes > 0)
		{
			$si = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
			return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . ' ' . $si[$i];
		}
		else
		{
			return 0;
		}
	}

	public function number($value, $decimals = 0)
	{
		return number_format($value, $decimals, '.', ',');
	}

	public function price($value, $useShort = false)
	{
		return $useShort ? money_format(static::PRICE_FORMAT_SHORT, $value) : money_format(static::PRICE_FORMAT, $value);
	}

	public function timestamp($unixTime, $useShort = false)
	{
		if( $unixTime > 0 )
			return $useShort ? strftime(static::TIMESTAMP_FORMAT_SHORT, $unixTime) : strftime(static::TIMESTAMP_FORMAT, $unixTime);

		return '-';
	}

	public function date($unixTime, $useShort = false)
	{
		if( $unixTime > 0 )
			return $useShort ? strftime(static::DATE_FORMAT_SHORT, $unixTime) : strftime(static::DATE_FORMAT, $unixTime);

		return '-';
	}

	public function hoursSince($seconds, $round = 1)
	{
		if(!is_numeric($round))
			$round = 1;

		$hours = round( $seconds / (60*60), $round );
		return sprintf(_('%s h'), $hours);
	}

	public function time($unixTime, $useShort = false)
	{
		if( $unixTime > 0 )
			return $useShort ? strftime(static::TIME_FORMAT_SHORT, $unixTime) : strftime(static::TIME_FORMAT, $unixTime);

		return '-';
	}
}
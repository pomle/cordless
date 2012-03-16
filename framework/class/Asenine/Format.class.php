<?
namespace Asenine;

class Format
{
	public static
		$CurrencyFilter,
		$NumberFilter,
		$PriceFilter,
		$TimeFilter;

	public static function init()
	{
		self::setLocale();
	}

	public static function setLocale($localeID = null)
	{
		self::$CurrencyFilter = self::$PriceFilter = self::$TimeFilter = self::$NumberFilter = self::getFilter($localeID);
	}

	public static function getFilter($language = null)
	{
		if( $language && ($classPath = 'Locale\\Format\\' . $language) && class_exists($classPath) )
			return new $classPath();

		return new Locale\Format\Common\Root();
	}

	public static function currency($value, $useShort = false)
	{
		return self::$CurrencyFilter->currency($value, $useShort);
	}

	public static function elapsedTime($seconds, $longMode = false)
	{
		return self::$TimeFilter->elapsedTime($seconds, $longMode);
	}

	public static function fileSize($bytes)
	{
		return self::$NumberFilter->fileSize($bytes);
	}

	public static function date($unixTime, $useShort = false)
	{
		return self::$TimeFilter->date($unixTime, $useShort);
	}

	public static function number($value)
	{
		return self::$NumberFilter->number($value);
	}

	public static function price($value, $useShort = false)
	{
		return self::$PriceFilter->price($value, $useShort);
	}

	public static function time($unixTime, $useShort = false)
	{
		return self::$TimeFilter->time($unixTime, $useShort);
	}

	public static function timestamp($unixTime, $useShort = false)
	{
		return self::$TimeFilter->timestamp($unixTime, $useShort);
	}
}

Format::init();
<?
namespace Element\Antiloop\Filter;

class Locale extends Select
{
	public static function country($isAllSelectable = false)
	{
		$Locale = new self('localeID', 'world', $isAllSelectable);
		$Locale->options += \Manager\Dataset\Locale::getCountries();
		return $Locale;
	}

	public static function language($isAllSelectable = false)
	{
		$Locale = new self('localeID', 'world', $isAllSelectable);
		$Locale->options += \Manager\Dataset\Locale::getLanguages();
		return $Locale;
	}
}
<?
namespace Asenine\Locale;

use \Asenine\DB as DB;

class Dataset
{
	public static function getAll()
	{
		$query = "SELECT * FROM Locales";
		return DB::queryAndFetchArray($query);
	}

	public static function getCountries()
	{
		$query = DB::prepareQuery("SELECT ID, country FROM Locales WHERE LENGTH(country) > 0 ORDER BY country COLLATE || ASC");
		return DB::queryAndFetchArray($query);
	}

	public static function getEnabled()
	{
		$query = "SELECT ID FROM Locales WHERE isEnabled = 1";
		return DB::queryAndFetchArray($query);
	}

	public static function getIdent()
	{
		$query = "SELECT ID, ident, country, language FROM Locales";
		return DB::queryAndFetchArray($query);
	}

	public static function getIDs()
	{
		$query = "SELECT ID FROM Locales";
		return DB::queryAndFetchArray($query);
	}

	public static function getLanguages()
	{
		$query = DB::prepareQuery("SELECT ID, language FROM Locales LENGTH(language) > 0 ORDER BY country COLLATE || ASC");
		return DB::queryAndFetchArray($query);
	}
}
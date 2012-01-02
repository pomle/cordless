<?
namespace Manager\Dataset;

abstract class _Common
{
	public static function getCount()
	{
		$query = "SELECT COUNT(*) FROM " . static::TABLE;
		$count = (int)\DB::queryAndFetchOne($query);
		return $count;
	}

	public static function getProperties($ID)
	{
		throw New \Exception(__METHOD__ . ' is dangerous and has been removed. Do not rely on');
		### $query = \DB::prepareQuery("SELECT * FROM " . static::TABLE . " WHERE ID = %u", $ID);
		### return \DB::queryAndFetchOne($query);
	}

	public static function isExisting($ID)
	{
		$query = \DB::prepareQuery("SELECT COUNT(*) FROM " . static::TABLE . " WHERE ID = %u", $ID);
		$isExisting = (bool)\DB::queryAndFetchOne($query);
		return $isExisting;
	}
}
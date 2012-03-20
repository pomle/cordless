<?
namespace Asenine\Manager;

class Cache
{
	public static $flushedCacheKeys = array();

	public static function flushEvents()
	{
		$eventNames = func_get_args();
		foreach($eventNames as $eventName)
		{
			self::flushEvent($eventName);
		}
	}

	public static function flushEvent()
	{
		$args = func_get_args();
		$cacheKeys = call_user_func_array(array('self', 'getEventCacheKeys'), $args);
		self::flushArray($cacheKeys);
	}

	public static function flushLocale($cacheKeyTemplate)
	{
		$query = \DB::prepareQuery("SELECT ID FROM Locales");
		$localeIDs = \DB::queryAndFetchArray($query);
		self::flushLoop($cacheKeyTemplate, $localeIDs);
	}

	public static function flushLoop($cacheKeyTemplate, $keys, $placeHolder = '%ID%')
	{
		foreach($keys as $key)
		{
			$cacheKey = str_replace($placeHolder, $key, $cacheKeyTemplate);
			self::flushOne($cacheKey);
		}
	}

	public static function flushOne($cacheKey)
	{
		self::$flushedCacheKeys[] = $cacheKey;
		\Cache::delete($cacheKey);
	}

	public static function flushSeveral()
	{
		self::flushArray(func_get_args());
	}

	public static function flushArray($cacheKeys)
	{
		foreach($cacheKeys as $cacheKey)
		{
			self::flushOne($cacheKey);
		}
	}

	public static function getEventCacheKeys()
	{
		$args = func_get_args();

		$eventName = array_shift($args);

		$events = self::getEvents();

		if( !isset($events[$eventName]) ) throw New \Exception('Cache Flush Event "' . $eventName . '" not found');

		$cacheKeys = array();

		include $events[$eventName];

		return $cacheKeys;
	}

	public static function getEventNames()
	{
		return array_keys(self::getEvents());
	}

	private static function getEvents()
	{
		$files = glob(ASENINE_DIR_COMMON . 'cache/*.inc.php');
		$events = array();
		foreach($files as $file)
		{
			$eventName = str_replace('.inc.php', '', basename($file));
			$events[$eventName] = $file;
		}
		return $events;
	}
}
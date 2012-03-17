<?
namespace Asenine;

class Cache
{
	const PROVIDER = 'Memcache';

	const KEY_MIN_LENGTH = 1;

	const DEFAULT_TIMEOUT = 0; // No-timeout
	const DEFAULT_COMPRESS = false;

	private static $memcached;
	public static $isEnabled = true;

	public static function init()
	{
		self::$isEnabled = !( defined('CACHE_ENABLED') && constant('CACHE_ENABLED') === false );

		if( !isset(self::$memcached) )
		{
			self::$memcached = new Memcache();
			self::$memcached->pconnect(CACHE_HOST, CACHE_PORT);
		}
	}

	public static function isEnabled()
	{
		return self::$isEnabled;
	}

	private static function prepareCacheKey($cacheKey)
	{
		return CACHE_PREFIX . '_' . $cacheKey;
	}

	public static function get($cacheKey)
	{
		if( !self::$isEnabled ) return false;

		if( CACHE_FORCE_REGENERATE === true ) return false;

		$cacheKey = self::prepareCacheKey($cacheKey);
		return self::$memcached->get($cacheKey);
	}

	public static function set($cacheKey, $data, $timeout = null)
	{
		if( strlen($cacheKey) == 0 )
		{
			trigger_error("Cache Key empty", E_USER_WARNING);
			return false;
		}

		$cacheKey = self::prepareCacheKey($cacheKey);

		if( is_null($timeout) )
		{
			$timeout = self::DEFAULT_TIMEOUT;
		}

		if( self::$memcached->set($cacheKey, $data, null, (int)$timeout) )
		{
			return true;
		}
		else
		{
			trigger_error(sprintf('Cache Set Failed: Key "%s", Data Size: %u bytes', $cacheKey, strlen(serialize($data))), E_USER_WARNING);
			return false;
		}
	}

	public static function delete($cacheKey)
	{
		$cacheKey = self::prepareCacheKey($cacheKey);
		self::$memcached->delete($cacheKey);
	}

	public static function flush()
	{
		self::$memcached->flush();
	}

	public static function getStats()
	{
		return self::$memcached->getStats();
	}
}

Cache::init();
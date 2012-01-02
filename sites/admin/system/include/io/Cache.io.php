<?
if( !$user->isAdministrator() ) throw New Exception('Only administrators can work with Cache');

interport('cacheKeyPreset', 'cacheKey', 'cacheData', 'localeID');

if( !Cache::isEnabled() )
{
	Message::addNotice(_('Cache is currently disabled with CACHE_ENABLED'));
}

switch($action)
{
	case 'showEvent':
		interport('eventName', 'eventArgs');
		$args = array_merge(array($eventName), $eventArgs);
		$cacheKeys = call_user_func_array(array('\\Manager\\Cache', 'getEventCacheKeys'), $args);

		if( count($cacheKeys) )
		{
			foreach($cacheKeys as $cacheKey)
			{
				Message::addNotice($cacheKey);
			}
		}
		else
		{
			Message::addAlert('[NONE]');
		}
		break;

	case 'triggerEvent':
		interport('eventName', 'eventArgs');
		$args = array_merge(array($eventName), $eventArgs);
		call_user_func_array(array('\\Manager\\Cache', 'flushEvent'), $args);

		if( count($cacheKeys = \Manager\Cache::$flushedCacheKeys) )
		{
			foreach($cacheKeys as $cacheKey)
			{
				Message::addNotice($cacheKey);
			}
		}
		else
		{
			Message::addAlert('No cache keys touched');
		}
		break;

	case 'read':
		Cache::$isEnabled = true;
		$cacheData = Cache::get($cacheKey);
		Message::addNotice(str_replace('%CACHE_KEY%', $cacheKey, _('Visar data för nyckel "%CACHE_KEY%"')));

		ob_start();

		var_dump($cacheData);

		$dumpedData = ob_get_clean();

		Message::addNotice('<pre>' . htmlspecialchars($dumpedData) . '</pre>');
		break;

	case 'write':
		if( strlen($cacheKey) == 0 ) throw New Exception(str_replace('%CACHE_KEY_MIN_LENGTH%', Cache::KEY_MIN_LENGTH, _('Nyckel måste vara minst %CACHE_KEY_MIN_LENGTH% tecken')));
		if( !Cache::set($cacheKey, $cacheData, 0) ) throw New Exception(_('Kunde inte sätta cache'));

		Message::addNotice(str_replace('%CACHE_KEY%', $cacheKey, _('Data för nyckel "%CACHE_KEY%" har satts')));
		break;

	case 'flush':
		if( strlen($cacheKey) > 0 )
		{
			Cache::delete($cacheKey);
			Message::addNotice(str_replace('%CACHE_KEY%', $cacheKey, _('Data för nyckel "%CACHE_KEY%" har rensats')));
		}
		else
		{
			if( $cacheKeyPreset == 'ALL' )
			{
				Cache::flush();
				Message::addNotice('Global Cache Flush Complete');
				Message::addAlert("Notice from PHP.net: Memcache::flushMessages()immediately invalidates all existing items. Memcache::flush() doesn't actually free any resources, it only marks all the items as expired, so occupied memory will be overwritten by new items.");
			}
			else
			{
				$cacheKeys = explode('|', $cacheKeyPreset);
				foreach($cacheKeys as &$cacheKey)
				{
					$cacheKey = sprintf($cacheKey, $localeID);
					Cache::delete($cacheKey);
				}
				Message::addNotice(str_replace('%CACHE_KEY%', join(', ', $cacheKeys), _('Data för nyckel "%CACHE_KEY%" har rensats')));
			}
		}
		break;
}
<?
function cachePurge()
{
	call_user_func_array(array('\\Manager\\Cache', 'flushEvent'), func_get_args());
	if( DEBUG )
	{
		$cacheKeys = \Manager\Cache::$flushedCacheKeys;
		foreach($cacheKeys as $cacheKey)
			Message::addNotice('Cache Purged for Cache Key "' . $cacheKey . '"');
	}
}

function requireClass()
{
	foreach(func_get_args() as $c)
		if( !class_exists($c) ) exit("$c does not exist");
}

function glob_rec($pattern, $path)
{
	$pattern = escapeshellarg($pattern);
	$path = escapeshellarg($path);
	return array_filter(explode("\n", shell_exec("find $path -name $pattern")));
}

function getMediaPinky($mediaHash = null, $mediaID = null)
{
	if( !$mediaHash && !$mediaID ) return false;

	### For now we only allow hash
	if( !$mediaHash ) return false;

	return \Media\Producer\CrossSite::createFromHash($mediaHash)->getPinky();
}

function interport() ### Imports values from $_GET or $_POST respectively and sets them into $GLOBALS.
{
	$varnames = func_get_args();
	foreach($varnames as $varname)
	{
		if( !isset($GLOBALS[$varname]) && (isset($_POST[$varname]) || isset($_GET[$varname])) )
		{
			$source = isset($_GET[$varname]) ? $_GET[$varname] : $_POST[$varname];
			$GLOBALS[$varname] = $source;
		}
	}
}
<?
function __autoload($className)
{
	$classPath = trim(str_replace('\\', '/', $className) . '.class.php', '/');
	include $classPath;
}

function addIncludePath($newPath)
{
	$currentIncludePaths = get_include_path();
	#set_include_path($currentIncludePaths . PATH_SEPARATOR . $newPath); ### New paths have lower priority
	set_include_path($newPath . PATH_SEPARATOR . $currentIncludePaths); ### New paths have higher priority
}

function asenineDef($const, $value)
{
	if( defined($const) ) return false;

	define($const, $value);

	return true;
}

function asenineLog($string, $space = 'Global')
{
	if( defined('DIR_LOG') )
	{
		$filename = DIR_LOG . $space . '.log';
		file_put_contents($filename, date('c: ') . $string . "\n", FILE_APPEND);
	}
}

function recGlob($pattern, $path)
{
	$command = sprintf("%s %s -name %s", 'find', escapeshellarg($path), escapeshellarg($pattern));
	#echo $command;
	return array_filter(explode("\n", shell_exec($command)));
}
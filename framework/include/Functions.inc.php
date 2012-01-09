<?
function __autoload($className)
{
	$classPath = trim(str_replace('\\', '/', $className) . '.class.php', '/');
	include $classPath;
}

function addIncludePath($newPath)
{
	$currentIncludePaths = get_include_path();
	set_include_path($currentIncludePaths . PATH_SEPARATOR . $newPath);
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

function sendFile($filePath, $fileName = '', $contentType = 'application/octet-stream')
{
	if( is_file($filePath) && is_readable($filePath) )
	{
		if( strlen($fileName = (string)$fileName) == 0)
			$fileName = basename($filePath);

		ini_set('zlib.output_compression', 'off');
		header('Accept-Ranges: bytes');
		header('Content-Type: '.$contentType);
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		header('Content-Length: '.filesize($filePath));
		header('X-LIGHTTPD-send-file: '.$filePath);
		exit();
	}

	return false;
}

function timeElapsed()
{
	return sprintf('%.5F', microtime(true) - RENDERSTART);
}

function timeDiff()
{
	static $lastTime;
	$thisTime = microtime(true);
	$diffTime = sprintf('%.5F', isset($lastTime) ? $thisTime - $lastTime : 0);
	$lastTime = microTime(true);
	return $diffTime;
}
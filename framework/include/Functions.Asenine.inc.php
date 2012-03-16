<?
namespace Asenine;

function getTempDir($prefix = null)
{
	$tmpFile = getTempFile($prefix);
	if( !unlink($tmpFile) || !mkdir($tmpFile) ) return false;
	return $tmpFile;
}

function getTempFile($prefix = null)
{
	return tempnam(DIR_TEMP, $prefix ? $prefix . '_' : null);
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
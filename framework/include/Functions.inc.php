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
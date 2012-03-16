<?
namespace Cordless;

$requireLogin = false;

function APIMethod()
{
	if(
		!defined('DIR_CORDLESS_API_METHODS')
		|| !file_exists(DIR_CORDLESS_API_METHODS)
		|| !is_dir(DIR_CORDLESS_API_METHODS)
	)
		throw New \Exception("DIR error");

	$files = glob(DIR_CORDLESS_API_METHODS . '/*');
	$methods = array();

	foreach($files as $filename)
		if( preg_match("%(.+)\.inc\.php$%", basename($filename), $match) )
			$methods[] = $match[1];

	return array('methods' => $methods);
}

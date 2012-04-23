<?
namespace Cordless;

function actionLink($text, $action)
{
	return sprintf('<a class="actionLink" href="#" onclick="javascript:%s; return false;">%s</a>', htmlspecialchars($action), $text);
}

function apiLink($method, $params = null)
{
	$getStr = '';
	if( is_array($params) )
		foreach($params as $key => $value)
			$getStr .= '&' . urlencode($key) . '=' . urlencode($value);

	return URL_API . "?method=" . urlencode($method) . $getStr;
}

function formatDuration($seconds)
{
	$minutes = floor($seconds / 60);
	return sprintf('%d:%02d', $minutes, $seconds % 60);
}

function formatCount($number)
{
	return number_format($number, 0, '', ' ');
}

function getLastFM()
{
	if( LAST_FM_API_KEY && LAST_FM_API_SECRET )
		return new \Asenine\API\LastFM(LAST_FM_API_KEY, LAST_FM_API_SECRET);

	return false;
}

function getUserTrackItemImageURL($mediaHash)
{
	try
	{
		return \Asenine\Media\Producer\Thumb::createFromHash($mediaHash)->getCustom(100, 100, true);
	}
	catch(\Exception $e)
	{
		return false;
	}
}


function libraryLink($text, $panel, $params = null)
{
	return sprintf('<a class="panelLibrary" href="%s">%s</a>',htmlspecialchars(libraryURL($panel, $params)), $text);
}

function libraryURL($panel, $params = null)
{
	$qs = '';

	if( is_array($params) )
	{
		foreach($params as $key => $value)
			$qs .= urlencode($key) . '=' . urlencode($value) . '&';
	}
	else
		$qs = $params;

	return URL_PLAYER . sprintf('ajax/Panel.php?type=Library&name=%s&%s', urlencode($panel), $qs);
}


function loadPanel($type, $name, $title = null)
{
	global $User;

	if( preg_match('/[^A-Za-z]/', $type) > 0 )
		throw new \Exception('Invalid Panel Type');

	if( preg_match('/[^A-Za-z\-]/', $name) > 0 )
		throw new \Exception('Invalid Panel Name');

	$includeFile = DIR_ELEMENT_PANEL . sprintf('%s.%s.inc.php', $type, $name);

	if( !file_exists($includeFile) )
		throw new \Exception(sprintf("File %s does not exist", $includeFile));

	require $includeFile;

	return;
}


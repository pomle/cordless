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

	return "/api/?method=" . urlencode($method) . $getStr;
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
	return new \Asenine\API\LastFM(LAST_FM_API_KEY, LAST_FM_API_SECRET);
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


function libraryLink($text, $panel, $qs = '')
{
	return sprintf('<a class="panelLibrary" href="/ajax/Panel.php?type=Library&amp;name=%s&%s">%s</a>', htmlspecialchars($panel), htmlspecialchars($qs), $text);
}

function loadPanel($type, $name, $title = null)
{
	global $User;

	try
	{
		if( preg_match('/[^A-Za-z]/', $type) > 0 )
			throw New \Exception('Invalid Panel Type');

		if( preg_match('/[^A-Za-z\-]/', $name) > 0 )
			throw New \Exception('Invalid Panel Name');

		$includeFile = DIR_ELEMENT_PANEL . sprintf('%s.%s.inc.php', $type, $name);

		if( !file_exists($includeFile) )
			throw New \Exception(sprintf("File %s does not exist", $includeFile));

		require $includeFile;

		return true;
	}
	catch(\Exception $e)
	{
		if( DEBUG ) die($e->getMessage());
		return false;
	}
}


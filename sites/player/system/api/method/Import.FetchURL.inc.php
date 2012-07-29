<?
namespace Cordless;

function APIMethod($User, $params)
{
	if( !$User->hasPolicy('AllowCordlessFetch') )
		throw New APIException(_("Fetch access denied by policy"));

	if( !isset($params->url) || strlen($params->url) < 5 || preg_match('%.+://.+%', $params->url) == 0 )
		throw New APIException(_("URL invalid"));

	$url = $params->url;
	$artist = isset($params->artist) && strlen($params->artist) ? $params->artist : null;
	$title = isset($params->title) && strlen($params->title) ? $params->title : null;
	$mimeType = false;


	if( preg_match('%youtube.com%', $url) )
	{
		$YT = new \Cordless\Util\YouTubeVideoURLParser($url);
		$url = reset($YT->urls);

		$doc = new \DOMDocument();
		@$doc->loadHTML($YT->html);

		$xpath = new \DOMXpath($doc);

		if( $str = $xpath->query("//meta[@name='title']")->item(0)->getAttribute('content') )
		{
			#throw new APIException($str);

			$nodes = explode('-', $str);

			if( count($nodes) == 2 )
			{
				$artist = $artist ?: trim($nodes[0]);
				$title = $title ?: trim($nodes[1]);
			}
		}
	}

	### Fetch headers first
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$header = curl_exec($curl);
	curl_close($curl);

	$mimeType = preg_match("%^Content-Type:(.+/.+)(;|$)%mUi", $header, $match) ? trim($match[1]) : null;

	if( !isset($params->ignoreHeader) || !$params->ignoreHeader )
	{
		if( !$mimeType )
			throw New APIException(_("Could not parse MIME type"));

		$allowTypes = array('audio', 'video');
		$allowFormats = array('ogg', 'mpeg', 'mp3', 'mpeg3', 'mp4');

		list($type, $format) = explode('/', $mimeType);

		if( !in_array(strtolower($type), $allowTypes) )
			throw New APIException(sprintf(_('MIME type was "%s", expected (%s)/*'), $mimeType, join('|', $allowTypes)));

		if( !in_array(strtolower($format), $allowFormats) )
			throw New APIException(sprintf(_('MIME format was "%s", expected */%s'), $mimeType, join('|', $allowFormats)));
	}

	try
	{
		$File = \Asenine\File::fromURL($url);
		$File->mime = $mimeType;

		$UserTrack = Event\UserTrack::importFile($User, $File, trim($artist), trim($title));
	}
	catch(\Exception $e)
	{
		throw new APIException($e->getMessage());
	}

	return (string)$UserTrack;
}
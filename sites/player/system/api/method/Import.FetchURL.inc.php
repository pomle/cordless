<?
namespace Cordless;

function APIMethod($User, $params)
{
	if( !$User->hasPolicy('AllowCordlessFetch') )
		throw New APIException(_("Fetch access denied by policy"));

	if( !isset($params['url']) || strlen($params['url']) < 5 || preg_match('%.+://.+%', $params['url']) == 0 )
		throw New APIException(_("URL invalid"));

	$url = $params['url'];

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$header = curl_exec($curl);
	curl_close($curl);


	$mimeType = preg_match("%^Content-Type:(.+/.+)(;|$)%mUi", $header, $match) ? trim($match[1]) : null;

	if( !isset($params['ignoreHeader']) || !$params['ignoreHeader'] )
	{
		if( !$mimeType )
			throw New APIException(_("Could not parse MIME type"));

		$allowFormats = array('ogg', 'mpeg', 'mp3');

		list($type, $format) = explode('/', $mimeType);

		if( strtolower($type) != "audio" )
			throw New APIException(sprintf(_('MIME type was "%s", expected audio/*'), $mimeType));

		if( !in_array(strtolower($format), $allowFormats) )
			throw New APIException(sprintf(_('MIME format was "%s", expected */%s'), $mimeType, join('|', $allowFormats)));
	}



	$File = \Asenine\File::fromURL($url);
	$File->mime = $mimeType;

	$UserTrack = Event\UserTrack::importFile($User, $File);

	return (string)$UserTrack;
}
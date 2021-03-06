<?
namespace Cordless;

require DIR_SITE_INCLUDE . 'ServeFilePartial.inc.php';
require DIR_SITE_INCLUDE . 'TrackPrepare.inc.php';

function APIMethod($User, $params)
{
	$UserTrack = getUserTrack($params, $User, true, false);

	$forceDownload = ( isset($params->download) && (bool)$params->download );

	$playFormat = $User->getSetting('Stream_Play_Format');
	$downloadFormat = $User->getSetting('Stream_Download_Format');


	if( isset($params->format) )
		$format = $params->format;

	elseif( $forceDownload && $downloadFormat )
		$format = $downloadFormat;

	elseif( $playFormat )
		$format = $playFormat;

	### Try to identify what format to send by the ACCEPT header
	elseif( isset($_SERVER['HTTP_ACCEPT']) && preg_match('%audio/(ogg|mp3)%', $_SERVER['HTTP_ACCEPT'], $match) )
		$format = $match[1];

	### If Opera or Firefox, send ogg
	elseif( isset($_SERVER['HTTP_USER_AGENT']) && preg_match('%(^Opera|Firefox)%', $_SERVER['HTTP_USER_AGENT'], $match) )
		$format = 'ogg';

	### Default to MP3
	else
		$format = 'mp3';

	### Opera does not play nicely with our partial content stream for some reason. This fixes so that is plays anything at all.
	if( isset($_SERVER['HTTP_USER_AGENT']) && preg_match('%^Opera%', $_SERVER['HTTP_USER_AGENT'], $match) )
		unset($_SERVER['HTTP_RANGE']);


	switch($format)
	{
		case 'ogg':
			$format = 'ogg';
			$contentType = 'audio/ogg';
			$ext = 'ogg';
		break;

		case 'mp3':
			$format = 'mp3';
			$contentType = 'audio/mp3';
			$ext = 'mp3';
		break;

		### Sends raw file from Media Library thus skipping next step
		case 'raw':
			$File = $UserTrack->Track->Audio->File;

			if( preg_match('/\.(mp3|ogg|mp4|wav)$/i', $File->name, $match) )
				$fileName = sprintf('%s.%s', $UserTrack, strtolower($match[1]));
			else
				$fileName = $File->name;

			serveFilePartial($File->location, $fileName, $File->mime);
			die();
		break;

		default:
			throw New APIException( sprintf('Unknown format "%s"', $format) );
	}


	$fileName = trackPrepare($UserTrack, $format);

	if( isset($params->prepare) && (bool)$params->prepare )
	{
		return array(
			'userTrackID' => $UserTrack->userTrackID,
			'format' => $format,
			'isPrepared' => file_exists($fileName)
		);
	}


	$fileTitle = sprintf('%s.%s', $UserTrack, $ext);

	header("X-Cordless-Artist: " . $UserTrack->artist);
	header("X-Cordless-Title: " . $UserTrack->title);

	serveFilePartial($fileName, $fileTitle, $contentType);
	die();
}
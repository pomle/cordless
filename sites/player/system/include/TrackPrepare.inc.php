<?
namespace Cordless;

function trackPrepare(UserTrack $UserTrack, $format)
{
	switch($format)
	{
		case 'ogg':
			$codec = 'libvorbis';
		break;

		case 'mp3':
			$codec = 'libmp3lame';
		break;

		default:
			throw New \Exception(sprintf('Unknown format "%s"', $format));
	}

	$mediaHash = $UserTrack->Track->Audio->mediaHash;

	$Archive = new \Asenine\Archive(sprintf('tracks/%s/', $format));

	$filePath = $Archive->getFilePath($mediaHash);
	$fileName = $filePath . $mediaHash;

	if( !file_exists($fileName) )
	{
		if( !file_exists($filePath) && !mkdir($filePath, 0755, true) )
			throw New \Exception("Could not create destination dir " . $filePath);

		$Factory = new \Asenine\Media\Generator\AudioTranscode($UserTrack->Track->Audio, $format, $codec, 128000, 44100, 2);

		if( !$Factory->saveToFile($fileName) )
			throw New \Exception("File Generation Failed");
	}

	return $fileName;
}
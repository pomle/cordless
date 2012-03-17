<?
require '../../../Init.inc.php';

header("Content-type: text/plain");

function tempSendFile($filePath, $fileName, $contentType)
{
	ini_set('zlib.output_compression', 'off');
	header("Expires: " . date('r', time() + 60*60*24*30));
	header('Accept-Ranges: bytes');
	header('Content-Type: '.$contentType);
	header('Content-Disposition: attachment; filename="'.$fileName.'"');
	header('Content-Length: '.filesize($filePath));
	echo file_get_contents($filePath);
	unlink($filePath);
}

function getLiveTemp()
{
	return tempnam(DIR_TEMP, 'LiveGen_');
}

if( !$Media = \Asenine\Media::loadFromDB($_GET['mediaID']) )
	die('Invalid Media ID');
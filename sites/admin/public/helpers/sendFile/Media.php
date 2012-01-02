<?
require '../../../Init.inc.php';

try
{
	if( !isset($_GET['mediaID']) && !isset($_GET['mediaHash']) )
		throw New Exception('No mediaID or mediaHash');

	$mediaID = isset($_GET['mediaHash']) ? \Manager\Dataset\Media::getIDFromHash($_GET['mediaHash']) : $_GET['mediaID'];

	if( !$Media = \Manager\Media::loadOneFromDB($mediaID) )
		throw New Exception('Media not found');

	$filePath = $Media->getFilePath();

	if( !file_exists($filePath) || !is_file($filePath) )
		throw New Exception('File not found');

	sendFile($filePath, $Media->getFileOriginalName());

	exit();
}
catch(Exception $e)
{
	die($e->getMessage());
}



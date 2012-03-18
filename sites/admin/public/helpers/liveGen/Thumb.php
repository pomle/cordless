<?
require '_Common.inc.php';

try
{
	if( !$Media instanceof \Asenine\Media\Type\_Visual )
		throw New Exception('Media Type Mismatch');

	$tempFile = getLiveTemp();

	$Generator = new \Asenine\Media\Generator\ImageResize($Media, 200, 200, false, 90);

	if( !$Generator->saveToFile($tempFile) )
		throw New Exception('Generation failed');

	tempSendFile($tempFile, sprintf('MediaID_%u_Thumb.jpg', $Media->mediaID), 'image/jpeg');
}
catch(Exception $e)
{
	if( isset($tempFile) && is_file($tempFile) ) unlink($tempFile);
	die($e->getMessage());
}
<?
require '_Common.inc.php';

try
{
	if( !$Media instanceof \Media\Common\Visual )
		throw New Exception('Media Type Mismatch');

	$tempFile = getLiveTemp();

	$Generator = new \Media\Generator\ImageResize($Media, 200, 200, false, 90);

	if( !$Generator->saveToFile($tempFile) )
		throw New Exception('Generation failed');

	tempSendFile($tempFile, sprintf('MediaID_%u_Thumb.jpg', $Media->mediaID), 'image/jpeg');
}
catch(Exception $e)
{
	if( is_file($tempFile) ) unlink($tempFile);
	die($e->getMessage());
}
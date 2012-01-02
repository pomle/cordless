<?
require '_Common.inc.php';

try
{
	if( !$Media instanceof \Media\Common\Visual )
		throw New Exception('Media Type Mismatch');

	$frames = min((int)abs($_GET['frames']) ?: 10, 10);
	$size = min((int)abs($_GET['size']) ?: 300, 300);

	$tempFile = getLiveTemp();

	$Generator = new \Media\Generator\VideoStrip($Media, $frames, $size);

	if( !$Generator->saveToFile($tempFile) )
		throw New Exception('Generation failed');

	tempSendFile($tempFile, sprintf('MediaID_%u_VideoStrip.jpg', $Media->mediaID), 'image/jpeg');
}
catch(Exception $e)
{
	if( is_file($tempFile) ) unlink($tempFile);
	die($e->getMessage());
}
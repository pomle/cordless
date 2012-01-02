<?
namespace Media;

class Defunct extends Common\Unidentified
{
	const TYPE = 'defunct';
	const DESCRIPTION = '[Unidentified]';

	public static function canHandleFile($filePath)
	{
		false;
	}

	public function getInfo()
	{
		return null;
	}
}
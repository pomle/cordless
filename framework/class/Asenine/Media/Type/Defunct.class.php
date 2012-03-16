<?
namespace Asenine\Media\Type;

class Defunct extends _Unidentified
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
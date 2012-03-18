<?
namespace Asenine\Media\Preset;

class Copy extends \Asenine\Media\Preset
{
	const NAME = 'copy';

	public function __construct($mediaHash, $ext)
	{
		$this->mediaHash = $mediaHash;
		$this->ext = '.' . $ext;
	}

	public function createFile($filepath)
	{
		if( !$Media = \Manager\Media::loadByHash($this->mediaHash) ) return false;

		$sourceFile = $Media->getFilePath();
		$destinationFile = $filepath;

		return copy($sourceFile, $destinationFile);
	}
}
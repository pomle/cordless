<?
namespace Asenine\Media\Preset;

class Rotatable extends \Asenine\Media\Preset
{
	const NAME = 'rotatable';

	public function __construct($mediaHash, $numFrames = 36, $frameHeight = 400, $quality = null)
	{
		$this->mediaHash = $mediaHash;
		$this->numFrames = abs($numFrames);
		$this->frameHeight = abs($frameHeight);
		$this->quality = abs($quality) ?: 80;
		$this->subPath = sprintf('%u_%u_%u/', $this->numFrames, $this->frameHeight, $this->quality);
		$this->ext = '.jpg';
	}

	public function createFile($filepath)
	{
		if( !$Media = \Manager\Media::loadByHash($this->mediaHash) ) return false;

		if( !$Media instanceof \Media\Rotate ) return false;

		$Factory = new \Media\Generator\RotateFrame($Media, $this->frameHeight, $this->numFrames, $this->quality);

		return $Factory->saveToFile($filepath);
	}
}
<?
namespace Asenine\Media\Preset;

class VideoStrip extends \Asenine\Media\Preset
{
	const NAME = 'videoStrip';

	public function __construct($mediaHash, $numFrames = 10, $size = 100)
	{
		$this->mediaHash = $mediaHash;
		$this->size = abs($size);
		$this->numFrames = abs($numFrames);
		$this->subPath = sprintf('%ux%u/', $this->numFrames, $this->size);
		$this->ext = '.jpg';
	}

	public function createFile($filepath)
	{
		if( !$Media = \Manager\Media::loadByHash($this->mediaHash) ) return false;

		if( !$Media instanceof \Media\Video ) return false;

		$Factory = new \Media\Generator\VideoStrip($Media, $this->numFrames, $this->size);

		return $Factory->saveToFile($filepath);
	}
}
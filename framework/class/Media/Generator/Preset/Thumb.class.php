<?
namespace Media\Generator\Preset;

class Thumb extends _Preset
{
	const NAME = 'thumb';

	public function __construct($mediaHash, $x, $y, $crop = false, $quality = 90)
	{
		$this->mediaHash = $mediaHash;
		$this->x = abs($x);
		$this->y = abs($y);
		$this->crop = (bool)$crop;
		$this->quality = abs($quality);
		$this->subPath = sprintf('%ux%ux%ux%u/', $this->x, $this->y, $this->crop, $this->quality);
		$this->ext = '.jpg';
	}

	public function createFile()
	{
		if( !$Media = \Manager\Media::loadByHash($this->mediaHash) ) return false;

		if( !$Media instanceof \Media\Common\Visual ) return false;

		$Factory = new \Media\Generator\ImageResize($Media, $this->x, $this->y, $this->crop, $this->quality);

		return $Factory->saveToFile(DIR_MEDIA . $this->getFilePath());
	}
}
<?
namespace Asenine\Media\Preset;

class Thumb extends \Asenine\Media\Preset
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
		if( !$Media = \Asenine\Media::loadByHash($this->mediaHash) )
			throw new \Asenine\MediaException(sprintf("mediaHash %s not found", $this->mediaHash));

		if( !$Media instanceof \Asenine\Media\Type\_Visual )
			throw new \Asenine\MediaException(get_class($Media) . ' not instance of "\\Asenine\\Media\\Type\\_Visual"');

		$Factory = new \Asenine\Media\Generator\ImageResize($Media, $this->x, $this->y, $this->crop, $this->quality);

		return $Factory->saveToFile(DIR_MEDIA . $this->getFilePath());
	}
}
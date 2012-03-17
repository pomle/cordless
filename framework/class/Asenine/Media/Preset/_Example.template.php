<?
die('Skeleton File');

namespace Asenine\Media\Preset;

abstract class _Skeleton extends \Asenine\Media\Preset
{
	const NAME = 'skeleton'; ### Put name of preset here. It is used when generating subfolder

	public function __construct($mediaHash, $x, $y, $crop = false, $quality = 90)
	{
		$this->mediaHash = $mediaHash; ### Always set mediaHash

		### Add any options
		$this->x = abs($x);
		$this->y = abs($y);
		$this->crop = (bool)$crop;
		$this->quality = abs($quality);
		$this->subPath = sprintf('%ux%ux%ux%u/', $this->x, $this->y, $this->crop, $this->quality); ### Make sure all options are used in subPath as to avoid collition
		$this->ext = '.jpg'; ### Specify extension of output file
	}

	public function createFile()
	{
		### Ensure everythings is okay, or throw an exception
		if( !$Media = \Manager\Media::loadByHash($this->mediaHash) ) return false;

		if( !$Media instanceof \Media\Common\Visual ) return false;

		### Set up Factory together with options
		$Factory = new \Media\Generator\ImageResize($Media, $this->x, $this->y, $this->crop, $this->quality);

		### Return path to file
		return $Factory->saveToFile(DIR_MEDIA . $this->getFilePath());
	}
}
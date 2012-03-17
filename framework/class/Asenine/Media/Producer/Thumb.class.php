<?
namespace Asenine\Media\Producer;

use \Asenine\Media\Preset;

class Thumb extends \Asenine\Media\Producer
{
	function getCustom($sizeX, $sizeY, $crop = false)
	{
		if( $crop )
			$Preset = new Preset\CroppedThumb($this->mediaHash, $sizeX, $sizeY);
		else
			$Preset = new Preset\AspectThumb($this->mediaHash, $sizeX, $sizeY);

		return $Preset->getURL();
	}
}
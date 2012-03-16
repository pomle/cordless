<?
namespace Asenine\Media\Producer;

class Thumb extends \Asenine\Media\Producer
{
	function getCustom($sizeX, $sizeY, $crop = false)
	{
		if( $crop )
			$Preset = new \Media\Generator\Preset\CroppedThumb($this->mediaHash, $sizeX, $sizeY);
		else
			$Preset = new \Media\Generator\Preset\AspectThumb($this->mediaHash, $sizeX, $sizeY);

		return $Preset->getURL();
	}
}
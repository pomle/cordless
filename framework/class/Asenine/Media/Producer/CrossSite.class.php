<?
namespace Asenine\Media\Producer;

class CrossSite extends Producer
{
	function getCustom($sizeX, $sizeY, $crop = false)
	{
		if( $crop )
			$Preset = new \Media\Generator\Preset\CroppedThumb($this->mediaHash, $sizeX, $sizeY);
		else
			$Preset = new \Media\Generator\Preset\AspectThumb($this->mediaHash, $sizeX, $sizeY);

		return $Preset->getURL();
	}

	function getIcon()
	{
		return $this->getCustom(48, 48, true);
	}

	function getPinky()
	{
		return $this->getCustom(80, 80, false);
	}

	function getThumb()
	{
		return $this->getCustom(200, 200, false);
	}
}
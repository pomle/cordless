<?
namespace Asenine\Media\Generator;

class RotateFrame extends \Asenine\Media\Generator
{
	protected
		$Rotate,
		$numFrames,
		$frames,
		$pxlH;

	public function __construct(\Media\Rotate $Rotate, $pxlH, $frames = null, $quality = 80)
	{
		$this->Rotate = $Rotate;

		$this->numFrames = $this->Rotate->getFrameCount();

		if( is_null($frames) )
			$this->frames = $this->numFrames;
		else
			$this->frames = (int)min(abs($frames), $this->numFrames);

		$this->pxlH = (int)abs($pxlH);
		$this->quality = abs($quality);
	}


	public function saveToFile($outFile)
	{
		$ImageGuru = new \App\ImageGuru();

		for($i = 0; $i < $this->frames; $i++)
		{
			### If not using all frames
			$frameIndex = round(($this->numFrames / $this->frames) * $i);
			$frame = $this->Rotate->getFrame($frameIndex);
			$ImageGuru->addInput($frame);
		}

		$options = array
		(
			sprintf('-thumbnail x%u', $this->pxlH),
			'-append',
			sprintf('-quality %u', $this->quality),
		);

		return $ImageGuru->writeFile($outFile, $options);
	}
}
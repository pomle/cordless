<?
namespace Asenine\Media\Generator;

class VideoStrip extends \Asenine\Media\Generator
{
	protected
		$Media,
		$frames,
		$tempFiles;


	public function __construct(\Media\Common\Visual $Media, $numFrames = 10, $boundingBox = 200)
	{
		$this->Media = $Media;

		$frameCount = $this->Media->getFrameCount();

		if( !$frameCount )
			throw New \Exception(sprintf('Invalid Framecount: %s', $frameCount));

		$numFrames = abs($numFrames);
		$numFrames = min($numFrames, $frameCount);

		### Time between frames
		$frameSpacing = ($frameCount / $numFrames);

		### Forward half a step to avoid first (probably black)
		$frameOffset = $frameSpacing / 2;

		### Populate timecodes
		for($i = 0; $i < $numFrames; $i++)
			$this->frames[] = round(($frameSpacing * $i) + $frameOffset);

		$this->boundingBox = abs($boundingBox);
	}

	public function __destruct()
	{
		if( isset($this->tempFiles) )
			foreach($this->tempFiles as $f)
				unlink($f);
	}


	public function saveToFile($outFile)
	{
		$ImageGuru = new \App\ImageGuru();

		foreach($this->frames as $frame)
		{
			$tempFile = $this->Media->getFrame($frame);
			$this->tempFiles[] = $tempFile;
			$ImageGuru->addInput($tempFile);
		}

		$options = array
		(
			sprintf("-thumbnail '%1\$ux%1\$u>'", $this->boundingBox),
			'+append',
			sprintf('-quality %u', 90)
		);

		### Concatenate all frames
		return $ImageGuru->writeFile($outFile, $options);
	}
}
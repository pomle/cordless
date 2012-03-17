<?
namespace Asenine\Media\Generator;

class VideoFrame extends \Asenine\Media\Generator
{
	protected
		$FFMPEG,
		$duration,
		$atTime;

	public
		$imageFormat;


	public function __construct(\Asenine\Media\Type\Video $Video, $atTime = null, $atProgress = null, $atFrame = null)
	{
		$this->FFMPEG = new \Asenine\App\FFMPEG();

		$file = $Video->getFilePath();

		$this->FFMPEG->addInput($file);

		$this->FFMPEG->options = array();

		if( !$this->duration = $Video->getDuration() )
			throw New \Exception(sprintf('%s could not determine duration of file %s', __METHOD__, $file));

		if( !$this->frames = $Video->getFrameCount() )
			throw New \Exception(sprintf('%s could not determine frame count of file %s', __METHOD__, $file));

		$this->atTime = 0; ### Defaults to getting first frame

		if( !is_null($atTime) )
			$this->seekTime($atTime);
		elseif( !is_null($atProgress) )
			$this->seekProgress($atProgress);
		elseif( !is_null($atFrame) )
			$this->seekFrame($atFrame);

		$this->imageFormat = 'image2';
	}


	public function getDuration()
	{
		return $this->duration;
	}


	public function saveToFile($outFile)
	{
		$this->FFMPEG->seekDuration($this->atTime);
		$this->FFMPEG->options = array
		(
			'-vframes 1',
			'-an',
			sprintf('-f %s', \escapeshellarg($this->imageFormat))
		);
		return $this->FFMPEG->writeFile($outFile);
	}

	public function seekFrame($index)
	{
		$index = min(abs($index), $this->frames);
		$atTime = $this->duration * ($index / $this->frames);
		return $this->seekTime($atTime);
	}

	public function seekProgress($atProgress)
	{
		$fraction = abs($atProgress) / 100;
		if($fraction > 1) $fraction = 1;
		if($fraction < 0) $fraction = 0;
		return $this->seekTime($this->duration * $fraction);
	}

	public function seekTime($atTime)
	{
		$time = abs($atTime);
		if( $time > $this->duration ) $time = $this->duration;
		$this->atTime = $time;
		return $this->atTime;
	}
}
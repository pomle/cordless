<?
namespace Asenine\Media\Generator;

class x264 extends \Asenine\Media\Generator
{
	protected
		$FFMPEG,
		$workDir;

	public
		$videoSizeX,
		$videoSizeY,
		$videoCodec,
		$videoBitrate,
		$audioCodec,
		$audioBitrate,
		$audioFreq;


	public function __construct(\Media\Video $Video, $sizeX = null, $sizeY = null)
	{
		$this->FFMPEG = new \App\FFMPEG();
		$this->FFMPEG->addInput($Video->getFilePath());

		$this->Video = $Video;

		$streamInfo = $this->FFMPEG->getStreamInfo();

		$sizeX = abs($sizeX);
		$sizeY = abs($sizeY);

		$origX = abs($streamInfo['video']['size']['x']);
		$origY = abs($streamInfo['video']['size']['y']);

		if( $origX == 0 || $origY == 0 ) throw New \Exception('Could not parse current size');

		$origAspect = $origX / $origY;

		### Avoid upscaling video
		$sizeX = min($sizeX, $origX);
		$sizeY = min($sizeY, $origY);

		### Size not given: use original
		if( $sizeX == 0 && $sizeY == 0 )
		{
			$sizeX = $origX;
			$sizeY = $origY;
		}

		### If any value given is null, calculate corresponding
		if( $sizeX == 0 ) $sizeX = $origX * ($sizeY / $origY);
		if( $sizeY == 0 ) $sizeY = $origY * ($sizeX / $origX);

		### Force original aspect ratio
		$newAspect = $sizeX / $sizeY;
		if( $newAspect > $origAspect ) $sizeX = round($sizeY * $origAspect);
		if( $newAspect < $origAspect ) $sizeY = round($sizeX / $origAspect);

		### Make sizez divisable by 2
		$sizeX = $sizeX - ($sizeX % 2);
		$sizeY = $sizeY - ($sizeY % 2);

		$this->videoSizeX = $sizeX;
		$this->videoSizeY = $sizeY;

		$this->videoCodec = 'libx264';
		$this->audioCodec = 'libfaac';

		$this->audioFreq = 41000;
	}

	public function __destruct()
	{
		if( isset($this->workDir) && file_exists($this->workDir) )
		{
			$tempFiles = glob($this->workDir . '/*');
			foreach($tempFiles as $tempFile)
			{
				unlink($tempFile);
			}
			rmdir($this->workDir);
		}
	}


	public function saveToFile($outFile)
	{
		if( !isset($this->workDir) ) $this->workDir = $this->FFMPEG->getTempDir();

		$prevDir = getcwd();
		chdir($this->workDir);

		try
		{
			$globalOptions = array
			(
				sprintf('-s %ux%u', $this->videoSizeX, $this->videoSizeY),
				sprintf('-vcodec %s', \escapeshellarg($this->videoCodec)),
				'-threads 0',
				'-f  mp4'
			);

			$crf = 22;

			$firstPassOptions = array
			(
				'-vpre fastfirstpass',
				'-pass 1',
				'-an', ### Disable audio for first pass
				sprintf('-crf %u', $crf)
			);

			$options = array_merge($globalOptions, $firstPassOptions);

			if( !$this->FFMPEG->writeFile(null, $options) ) throw New \Exception('First Pass Failed: ' . end(\App\FFMPEG::$lastOutput));

			### First Pass Success

			$ffmpegOutput = \App\FFMPEG::$lastOutput;

			preg_match('%kb/s:([0-9]+)%', end($ffmpegOutput), $match);
			$firstPassBitrate = $match[1] * 1000; ### Convert from kb/s to bps

			$resHD = (1280 * 720);
			$resSD = (720 * 400);
			$resLD = (320 * 240);

			$videoBitrates = array
			(
				$resHD => 4096000,
				$resSD => 2048000,
				$resLD => 1024000,
				0 => 512000
			);

			$audioBitrates = array
			(
				$resSD => 192000,
				$resLD => 96000,
				0 => 64000
			);

			$streamInfo = $this->FFMPEG->getStreamInfo();
			$inputPixelArea = ($streamInfo['video']['size']['x'] * $streamInfo['video']['size']['y']);
			$audioChannels = $streamInfo['audio']['channels'];


			foreach($videoBitrates as $maxPixelArea => $maxVideoBitrate)
			{
				if( $inputPixelArea >= $maxPixelArea ) break;
			}
			$videoBitrate = $maxVideoBitrate;

			foreach($audioBitrates as $maxPixelArea => $maxAudioBitrate)
			{
				if( $inputPixelArea >= $maxPixelArea ) break;
			}
			$audioBitrate = $maxAudioBitrate;

			if( $firstPassBitrate > 0 )
			{
				$videoBitrate = sqrt($maxVideoBitrate / $firstPassBitrate) * $firstPassBitrate;
			}

			$videoBitrate = min($videoBitrate, $maxVideoBitrate);
			$audioBitrate = min($audioBitrate, $maxAudioBitrate);

			if( $audioChannels == 1 )
				$audioBitrate /= 2;

			$secondPassOptions = array
			(
				'-vpre hq',
				'-pass 2',
				sprintf('-b %u', $videoBitrate),
				sprintf('-ab %u', $audioBitrate),
				sprintf('-acodec %s', \escapeshellarg($this->audioCodec)),
				sprintf('-ar %u', $this->audioFreq),
				sprintf('-ac %u', $audioChannels) ### Two-channel audio
			);

			$options = array_merge($globalOptions, $secondPassOptions);

			if( !$this->FFMPEG->writeFile($outFile, $options) ) throw New \Exception('Second Pass Failed: ' . end(\App\FFMPEG::$lastOutput));

			### Second Pass Success

			chdir($prevDir);

			return true;
		}
		catch(\Exception $e)
		{
			chdir($prevDir);
			trigger_error(get_called_class() . '::' . __FUNCTION__ . ', mediaHash: ' . $this->Video->mediaHash . ' failed, Reason: ' . $e->getMessage(), E_USER_WARNING);

			return false;
		}
	}
}
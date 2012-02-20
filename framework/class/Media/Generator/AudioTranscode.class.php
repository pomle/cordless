<?
namespace Media\Generator;

class AudioTranscode extends _Generator
{
	protected
		$FFMPEG;

	public
		$codec,
		$bitrate,
		$frequency,
		$channels;

	public function __construct(\Media\Common\Audible $Audio, $format, $codec, $bitrate, $frequency = 44100, $channels = 2)
	{
		$this->FFMPEG = new \App\FFMPEG();
		$this->FFMPEG->addInput($Audio->getFilePath());

		$this->Audio = $Audio;

		$this->format = $format;
		$this->codec = $codec;
		$this->bitrate = $bitrate;

		$this->frequency = $frequency;
		$this->channels = $channels;
	}


	public function saveToFile($outFile)
	{
		try
		{
			$options = array
			(
				sprintf('-f %s', \escapeshellarg($this->format)),
				sprintf('-acodec %s', \escapeshellarg($this->codec)),
				sprintf('-ab %u', $this->bitrate),
				sprintf('-ar %u', $this->frequency),
				sprintf('-ac %u', $this->channels)
			);

			if( !$this->FFMPEG->writeFile($outFile, $options) )
				throw New \Exception('Transcoding Failed: ' . end(\App\FFMPEG::$lastOutput));

			return true;
		}
		catch(\Exception $e)
		{
			trigger_error(get_called_class() . '::' . __FUNCTION__ . ', mediaHash: ' . $this->Audio->mediaHash . ' failed, Reason: ' . $e->getMessage(), E_USER_WARNING);

			return false;
		}
	}
}
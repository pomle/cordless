<?
namespace Asenine\Media\Generator;

class AudioTranscode extends \Asenine\Media\Generator
{
	protected
		$FFMPEG;

	public
		$codec,
		$bitrate,
		$frequency,
		$channels;


	public function __construct(\Asenine\Media\Type\_Audible $Audio, $format, $codec, $bitrate, $frequency = 44100, $channels = 2)
	{
		$this->FFMPEG = new \Asenine\App\FFMPEG();
		$this->FFMPEG->addInput($Audio->File);

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
				throw New \Exception('Transcoding Failed: ' . end(\Asenine\App\FFMPEG::$lastOutput));

			return true;
		}
		catch(\Exception $e)
		{
			trigger_error(get_called_class() . '::' . __FUNCTION__ . ', mediaHash: ' . $this->Audio->mediaHash . ' failed, Reason: ' . $e->getMessage(), E_USER_WARNING);

			return false;
		}
	}
}
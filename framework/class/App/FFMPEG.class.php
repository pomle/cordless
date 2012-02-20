<?
namespace App;

if( !defined('EXECUTABLE_FFMPEG') )
	define('EXECUTABLE_FFMPEG', exec('which ffmpeg'));

if( !defined('EXECUTABLE_FFPROBE') )
	define('EXECUTABLE_FFPROBE', exec('which ffprobe') ?: EXECUTABLE_FFMPEG);


class FFMPEG extends Common\Root
{
	protected static
		$exe_ffprobe,
		$exe_ffmpeg;

	protected
		$optPre,
		$optPost,
		$streamInfo;

	public $options = array();


	public static function doEncode($inputFiles = array(), $outputFile = null, Array $optPre = array(), Array $optPost = array())
	{
		if( !self::$exe_ffmpeg ) return false;

		foreach($inputFiles as &$f)
			$f = '-i ' . \escapeshellarg($f);

		$command = sprintf('%s -y %s %s %s %s 2>&1', self::$exe_ffmpeg, join(' ', $optPre), join($inputFiles), join(' ', $optPost), $outputFile ? \escapeshellarg($outputFile) : '');

		asenineLog($command, 'FFMPEG');

		return self::runCommand($command);
	}

	public static function doInfo($inputFile)
	{
		if( !self::$exe_ffprobe ) return false;

		$command = sprintf('%s -i %s 2>&1', self::$exe_ffprobe, \escapeshellarg($inputFile));

		self::runCommand($command); ### If FFPROBE does not exist, we fall back to FFMPEG parsing, which returns false if no output file is specified but still provides parse:able output

		$returnData = self::$lastOutput;

		### If there are any misdetection warnings, we abort. We want to be sure
		if( count(preg_grep('/misdetection/', $returnData)) > 0 )
			return false;

		$inputs = preg_grep('/Input/', $returnData);

		### FFMPEG could not identify any reasonable input
		if( count($inputs) == 0 ) return false;


		if( preg_match('/Input.*#[0-9]+, (image2)/', reset($inputs)) ) ### JPEG can be detected as valid, but should not
			return false;

		$streams['interleave'] = array_values(preg_grep('%Duration:%', $returnData));
		$streams['audio'] = array_values(preg_grep('%Stream #[0-9]\.[0-9](.*): Audio%', $returnData));
		$streams['video'] = array_values(preg_grep('%Stream #[0-9]\.[0-9](.*): Video%', $returnData));

		if( preg_match('%Duration: (([0-9]{2}):([0-9]{2}):([0-9]{2})\.([0-9]{2})).*%', $streams['interleave'][0], $duration) )
		{
			$time = array
			(
				'c' => $duration[1],
				'h' => (int)$duration[2],
				'm' => (int)$duration[3],
				's' => (int)$duration[4],
				'f' => (float)($duration[5] / 100)
			);
			$duration = ($time['h'] * 3600) + ($time['m'] * 60) + $time['s'] + $time['f'];
		}

		if( preg_match('%bitrate: ([0-9]+)(.*)/%', $streams['interleave'][0], $bitrate) )
			$bitrate = (int)($bitrate[1] * 1000);

		$video = null;
		if( count($streams['video']) > 0 )
		{
			### Select the first video stream
			$video = reset($streams['video']);
			list($videoFormat, $videoColor, $videoSize) = explode(', ', substr($video, strpos($video, 'Video:') + 7));

			if( preg_match('/([0-9\.]+).(fps|tbr)/', $video, $fps) )
				$fps = (float)$fps[1];

			### Parse video size
			preg_match('/Video.*([0-9]+)x([0-9]+)[^0-9]/U', $video, $size);
			$videoSize = array('x' => $size[1], 'y' => $size[2]);
			#var_dump($size);

			$pixelAspectRatio = preg_match('/PAR ([0-9]+):([0-9]+)/', $video, $par) ? $par[1]/$par[2] : 1.0;
			$displayAspectRatio = preg_match('/DAR ([0-9]+):([0-9]+)/', $video, $dar) ? $dar[1]/$dar[2] : 1.0;

			$frames = null;
			if( is_numeric($duration) && is_numeric($fps) )
				$frames = floor($duration * $fps);

			$video = array
			(
				'size' => $videoSize,
				'fps' => $fps,
				'frames' => $frames,
				'aspect' => array
				(
					'pixel' => $pixelAspectRatio,
					'display' => $displayAspectRatio
				)
			);
		}

		$audio = null;
		if( count($streams['audio']) > 0 )
		{
			### Select the first audio stream
			$audio = reset($streams['audio']);
			list($audioFormat, $audioFrequency, $audioChannels, $audioBitrate) = explode(', ', substr($audio, strpos($audio, 'Audio:') + 7));

			$audio = array
			(
				'frequency' => (int)$audioFrequency,
				'format' => $audioFormat,
				'channels' => ($audioChannels == 'mono' ? 1 : 2)
			);
		}

		return array
		(
			'bitrate' => $bitrate,
			'duration' => $duration,
			'time' => $time,
			'video' => $video,
			'audio' => $audio
		);
	}

	public static function init($ffmpeg = null, $ffprobe = null)
	{
		if( $ffmpeg && is_executable($ffmpeg) )
			self::$exe_ffmpeg = $ffmpeg;
		else
			trigger_error(sprintf('%s: could not initialize FFMPEG', __METHOD__), E_USER_WARNING);

		if( $ffprobe && is_executable($ffprobe) )
			self::$exe_ffprobe = $ffprobe;
		else
			trigger_error(sprintf('%s: could not initialize FFPROBE', __METHOD__), E_USER_WARNING);

		return true;
	}

	public static function isValidFile($filename)
	{
		return (bool)(is_file($filename) && is_readable($filename) && self::doInfo($filename));
	}


	public function __construct()
	{
		$this->optPre = $this->optPost = array();
	}


	public function getStreamInfo()
	{
		if( !isset($this->streamInfo) ) $this->streamInfo = self::doInfo(reset($this->inputFiles));
		return $this->streamInfo;
	}

	public function seekDuration($duration)
	{
		$hour = 60*60;
		$hours = floor($duration / $hour);
		$duration -= $hours*$hour;

		$minute = 60;
		$minutes = floor($duration / $minute);
		$duration -= $minutes*$minute;

		$second = 1;
		$seconds = floor($duration / $second);
		$duration -= $seconds*$second;

		$this->optPre['seek'] = sprintf('-ss %02u:%02u:%05.2F', $hours, $minutes, $seconds + $duration);
		return $this;
	}

	public function writeFile($outFile = null, Array $options = array())
	{
		if( $outFile ) $tempFile = $this->getTempFile();

		$optPre = $this->optPre;
		$optPost = $options ?: $this->options ?: $this->optPost;

		if( self::doEncode($this->inputFiles, $tempFile ?: '/dev/null', $optPre, $optPost) )
		{
			if( is_null($outFile) ) return true;

			if( rename($tempFile, $outFile) )
			{
				chmod($outFile, FILE_CREATE_PERMS);
				return true;
			}
		}

		if( file_exists($tempFile) ) unlink($tempFile);
		return false;
	}
}

FFMPEG::init(EXECUTABLE_FFMPEG, EXECUTABLE_FFPROBE);
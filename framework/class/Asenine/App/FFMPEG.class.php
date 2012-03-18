<?
namespace Asenine\App;

if( !defined('EXECUTABLE_FFMPEG') )
	define('EXECUTABLE_FFMPEG', exec('which ffmpeg'));

if( !defined('EXECUTABLE_FFPROBE') )
	define('EXECUTABLE_FFPROBE', exec('which ffprobe') ?: EXECUTABLE_FFMPEG);


class FFMPEG extends Common\Root
{
	protected static
		$exe_ffprobe,
		$exe_ffmpeg,
		$formats;

	protected
		$optPre,
		$optPost,
		$streamInfo;

	public $options = array();


	public static function doEncode($inputFiles = array(), $outputFile = null, Array $optPre = array(), Array $optPost = array())
	{
		if( !self::$exe_ffmpeg ) return false;

		foreach($inputFiles as &$f)
			$f = self::getInputCommand($f);

		$command = sprintf('%s -y %s %s %s %s 2>&1', self::$exe_ffmpeg, join(' ', $optPre), join($inputFiles), join(' ', $optPost), $outputFile ? \escapeshellarg($outputFile) : '');

		asenineLog($command, 'FFMPEG');

		return self::runCommand($command);
	}

	public static function doInfo($inputFile)
	{
		if( !self::$exe_ffprobe ) return false;

		$command = sprintf('%s %s 2>&1', self::$exe_ffprobe, self::getInputCommand($inputFile));

		#var_dump($command);
		self::runCommand($command); ### If FFPROBE does not exist, we fall back to FFMPEG parsing, which returns false if no output file is specified but still provides parse:able output

		$returnData = self::$lastOutput;

		### If there are any misdetection warnings, we abort. We want to be sure
		if( count(preg_grep('/misdetection/', $returnData)) > 0 )
			return false;

		$inputs = preg_grep('/Input/', $returnData);

		### FFMPEG could not identify any reasonable input
		if( count($inputs) == 0 ) return false;


		if( !preg_match('/Input.*#[0-9]+, (\w+),/', reset($inputs), $matches) ) ### JPEG can be detected as valid, but should not
			return false; ### Unexpected input string

		### Parse format from FFMPEGs result
		$format = $matches[1];

		if( in_array($format, array('image2')) ) return false; ### FFMPEG format exclude list to avoid some JPEGs being catched


		$streams['interleave'] = array_values(preg_grep('%Duration:%', $returnData));
		$streams['audio'] = array_values(preg_grep('%Stream #[0-9]\.[0-9](.*): Audio%', $returnData));
		$streams['video'] = array_values(preg_grep('%Stream #[0-9]\.[0-9](.*): Video%', $returnData));

		$time = null;
		$duration = null;
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
			'format' => $format,
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

	public static function getFormats()
	{
		while( !isset(self::$formats) ) ### After this loop has finished we've got a nice array with formats, or an empty array if problems occured, and we will only run once per php execution session, which should be enough since we don't expect FFMPEG to change
		{
			self::$formats = array(); ### Set the variable so that this loop will only run once per session

			if( !self::$exe_ffprobe ) break; ### No exe? NO FORMATS


			$cmd = sprintf('%s -formats', self::$exe_ffprobe);

			exec($cmd, $output, $retval);
			if( $retval != 0 ) break; ### exec call failed

			foreach($output as $line)
				if( preg_match('/\s[D\s][E\s]\s(\S+)/', $line, $match) )
					self::$formats[] = $match[1];

			break;
		}

		return self::$formats;
	}

	public static function getInputCommand($File)
	{
		$str = '';

		### If input is a \File we check for mime type. Decreases the chance of identification problems. Notice that mime type doesn't correspond exactly to FFMPEG formats. Some further logic may be needed here for some files
		if( $File instanceof \Asenine\File && $format = self::guessFormat($File) )
		{
			$formats = self::getFormats();
			if( in_array($format, $formats) )
				$str = sprintf('-f %s ', \escapeshellarg($format));
		}

		$str .= sprintf('-i %s', \escapeshellarg($File));

		return $str;
	}

	public static function guessFormat(\Asenine\File $File)
	{
		if( isset($File->name) && preg_match('/.(\w+)$/', $File->name, $matches) )
		{
			$ext = mb_strtolower($matches[1]);

			switch($ext)
			{
				case 'mp3':
					return 'mp3';
				break;
			}
		}

		if( isset($File->mime) && preg_match('%(.+)/(.+)%', $File->mime, $matches) )
		{
			list($mime, $type, $format) = $matches;
			return $format;
		}

		return false;
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
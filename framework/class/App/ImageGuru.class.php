<?
namespace App;

if( !defined('EXECUTABLE_IDENTIFY') )
	define('EXECUTABLE_IDENTIFY', exec('which identify'));

if( !defined('EXECUTABLE_CONVERT') )
	define('EXECUTABLE_CONVERT', exec('which convert'));

if( !defined('EXECUTABLE_JHEAD') )
	define('EXECUTABLE_JHEAD', exec('which jhead'));


class ImageGuru extends Common\Root
{
	protected static
		$exe_convert,
		$exe_identify,
		$exe_jhead;

	public $imageInfo = null;


	public static function doIdentify($filename)
	{
		if( !self::$exe_identify ) return false;

		$command = sprintf('%s %s', self::$exe_identify, escapeshellarg($filename));

		if( !self::runCommand($command) )
			return false;

		$widths = array();
		$heights = array();
		foreach(self::$lastOutput as $line)
		{
			if( preg_match('/([0-9]+)x([0-9]+)/', $line, $match) )
			{
				$widths[] = (int)$match[1];
				$heights[] = (int)$match[2];
			}
		}

		$width = max($widths);
		$height = max($heights);

		$info = explode(' ', $line);

		$infoArray = array
		(
			'format' => $info[1],
			'size' => array
			(
				'x' => (int)$width,
				'y' => (int)$height,
				'width' => (int)$width,
				'height' => (int)$height
			)
		);

		$orientation = 0;

		for(;;)
		{
			if( !self::$exe_jhead )
				break;

			$command = sprintf('%s %s', self::$exe_jhead, escapeshellarg($filename));

			if( !self::runCommand($command) )
				break;

			if( !$o = reset(preg_grep('/Orientation/', self::$lastOutput)) )
				break;

			if( !preg_match('/[0-9]+/', $o, $m) )
				break;

			$orientation = (int)$m[0];

			break;
		}

		$infoArray['orientation'] = $orientation;

		return $infoArray;
	}

	public static function doConvert($inputFiles, $outputFile, array $options = array(), $format)
	{
		if( !self::$exe_convert ) return false;

		$inputFiles = (array)$inputFiles;

		foreach($inputFiles as &$f)
			$f = \escapeshellarg($f);

		$command = sprintf('%s %s %s %s:%s', self::$exe_convert, join(' ', $inputFiles), join(' ', $options), $format, \escapeshellarg($outputFile));

		asenineLog($command, 'ImageGuru');

		if( self::runCommand($command) && file_exists($outputFile) && filesize($outputFile) > 0 )
			return true;

		return false;
	}

	public static function init($identify = null, $convert = null, $jhead = null)
	{
		if( $identify && is_executable($identify) )
			self::$exe_identify = $identify;
		else
			trigger_error(sprintf('%s: could not initialize identify', __METHOD__), E_USER_WARNING);

		if( $convert && is_executable($convert) )
			self::$exe_convert = $convert;
		else
			trigger_error(sprintf('%s: could not initialize convert', __METHOD__), E_USER_WARNING);

		### jhead is optional
		if( $jhead && is_executable($jhead) )
			self::$exe_jhead = $jhead;
		else
			trigger_error(sprintf('%s: jhead is not available - No Auto Orientation will occur', __METHOD__), E_USER_NOTICE);

		return true;
	}

	public static function isValidFile($filename)
	{
		return (bool)(is_file($filename) && is_readable($filename) && self::doIdentify($filename));
	}


	public function __construct()
	{
		$this->options = array();
	}


	public function getImageInfo()
	{
		if( !isset($this->imageInfo) ) $this->imageInfo = self::doIdentify(reset($this->inputFiles));
		return $this->imageInfo;
	}


	public function setFormat($string)
	{
		$this->format = $string;
	}

	public function setSize($w = null, $h = null)
	{
		$this->options['resize'] = sprintf
		(
			'%sx%s',
			(int)abs($w) ?: '',
			(int)abs($h) ?: ''
		);
	}

	public function writeFile($outFile, Array $options = array())
	{
		$tempFile = $this->getTempFile();

		if( empty($outFile) )
		{
			trigger_error(get_called_class() . __FUNCTION__ . " expected argument 1 to be destination file, \"$outFile\" given", E_USER_WARNING);
			return false;
		}

		if( count($options) == 0 )
		{
			foreach($this->options as $name => $value)
			{
				$options[] = sprintf('-%s %s', $name, escapeshellarg($value));
			}
		}

		$options[] = '-colorspace RGB'; // We don't do CMYK
		$options[] = '-strip'; // Strips all metadata from graphics

		if( self::doConvert($this->inputFiles, $tempFile, $options, $this->format ?: 'JPG') && rename($tempFile, $outFile) )
		{
			chmod($outFile, FILE_CREATE_PERMS);
			return true;
		}
		else
		{
			if( file_exists($tempFile) ) unlink($tempFile);
			return false;
		}
	}
}

ImageGuru::init(EXECUTABLE_IDENTIFY, EXECUTABLE_CONVERT, EXECUTABLE_JHEAD);
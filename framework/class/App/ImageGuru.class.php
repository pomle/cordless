<?
namespace App;

if( !defined('EXECUTABLE_IDENTIFY') )
	define('EXECUTABLE_IDENTIFY', exec('which identify'));

if( !defined('EXECUTABLE_CONVERT') )
	define('EXECUTABLE_CONVERT', exec('which convert'));


class ImageGuru extends Common\Root
{
	public $imageInfo = null;


	public static function doIdentify($filename)
	{
		if( !defined('EXECUTABLE_IDENTIFY') || !is_executable($bin = constant('EXECUTABLE_IDENTIFY')) )
		{
			trigger_error(sprintf('"%s" is not a valid executable', EXECUTABLE_IDENTIFY), E_USER_WARNING);
			return false;
		}

		$command = sprintf('%s %s', $bin, escapeshellarg($filename));

		if( self::runCommand($command) )
		{
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

			return array
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
		}
		else
		{
			return false;
		}
	}

	public static function doConvert($inputFiles, $outputFile, array $options = array(), $format)
	{
		$inputFiles = (array)$inputFiles;

		if( !defined('EXECUTABLE_CONVERT') || !is_executable($bin = constant('EXECUTABLE_CONVERT')) )
		{
			trigger_error(sprintf('"%s" is not a valid executable', EXECUTABLE_CONVERT), E_USER_WARNING);
			return false;
		}

		foreach($inputFiles as &$f)
			$f = \escapeshellarg($f);

		$command = sprintf('%s %s %s %s:%s', $bin, join(' ', $inputFiles), join(' ', $options), $format, \escapeshellarg($outputFile));

		asenineLog($command, 'ImageGuru');

		if( self::runCommand($command) && file_exists($outputFile) && filesize($outputFile) > 0 )
			return true;

		return false;
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
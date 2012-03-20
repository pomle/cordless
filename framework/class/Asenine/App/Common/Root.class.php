<?
namespace Asenine\App\Common;

if( !defined('FILE_CREATE_PERMS') ) define('FILE_CREATE_PERMS', 0755);

interface _Root
{
	public static function isValidFile($filename);
}

abstract class Root implements _Root
{
	protected $inputFiles = array();

	public static
		$lastCommand,
		$lastAnswer,
		$lastOutput,
		$lastExitCode;

	public static
		$verbose = false;


	final protected static function runCommand($command)
	{
		if( self::$verbose ) echo $command, "\n";
		self::$lastOutput = array();
		self::$lastCommand = $command;
		self::$lastAnswer = exec($command, self::$lastOutput, $exitCode);
		self::$lastExitCode = $exitCode;
		return !(bool)$exitCode; // Returns true if 0, false if not
	}


	final public function addInputs(array $filenames)
	{
		foreach($filenames as $filename)
			$this->addInput($filename);
	}

	final public function addInput($filename)
	{
		if( !is_file($filename) ) throw New \Exception("\"$filename\" is not a valid file");
		if( !is_readable($filename) ) throw New \Exception("\"$filename\" is not readable");

		$this->inputFiles[] = $filename;
	}

	final public function getTempDir()
	{
		$tmpFile = self::getTempFile();
		if( !unlink($tmpFile) || !mkdir($tmpFile) ) throw New \Exception('Failed to create temporary dir at ' . $tmpFile);
		return $tmpFile;
	}

	final public function getTempFile()
	{
		$prefix = str_replace('\\', '_', get_called_class()) . '_';
		$tmpFile = tempnam(ASENINE_DIR_TEMP, $prefix);
		if( $tmpFile === false ) throw New \Exception('Failed to create temporary file in ' . ASENINE_DIR_TEMP);
		return $tmpFile;
	}

	final public function isInputValid()
	{
		foreach($this->inputFiles as $file)
		{
			if( !static::isValidFile($file) ) return false;
		}
		return true;
	}
}
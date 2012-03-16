<?
namespace Asenine\App;

class PNG extends Common\Root
{
	/*
	public function PNGCrush($outFile) {

		$inFile = $this->getInputFile();
		$tempFile = $this->getTempFile('PNGCrush');

		if( !\FileManager::validateReadFile($inFile) ) return false;

		$app = $this->getApplication('PNGCrush');

		$command = "$app " . escapeshellarg($inFile) . ' ' . escapeshellarg($tempFile);

		exec($command, $returnData, $exitStatus);

		return \FileManager::validateWrittenFile($tempFile, $outFile);
	}

	public function PNGNQ($outFile, $colors = 64) {

		$inFile = $this->getInputFile();
		$tempFile = $this->getTempFile('PNGCrush');

		if( !\FileManager::validateReadFile($inFile) ) return false;

		$app = $this->getApplication('PNGNQ');

		$command = "cat " . escapeshellarg($inFile) . " | " . $app . ' ' . sprintf('-n %u', $colors) . ' > ' . $tempFile;

		exec($command, $returnData, $exitStatus);

		return \FileManager::validateWrittenFile($tempFile, $outFile);
	}*/

	public static function doCrush($inputFile, $outputFile, array $options = array())
	{
		if( !defined('EXECUTABLE_PNGCRUSH') || !is_executable($bin = constant('EXECUTABLE_PNGCRUSH')) ) throw New \Exception(EXECUTABLE_PNGCRUSH . ' not valid executable');

		$command = sprintf('%s %s %s %s', $bin, escapeshellarg($inputFile), join(' ', $options), escapeshellarg($outputFile));

		return self::runCommand($command);
	}

	public static function doNQ($inputFile, $outputFile, array $options = array())
	{
		if( !defined('EXECUTABLE_PNGNQ') || !is_executable($bin = constant('EXECUTABLE_PNGNQ')) ) throw New \Exception(EXECUTABLE_PNGNQ . ' not valid executable');

		$command = sprintf('cat %3$s | %1$s %2$s > %4$s', $bin, join(' ', $options), escapeshellarg($inputFile), escapeshellarg($outputFile));

		return self::runCommand($command);
	}

	public static function isValidFile($filename)
	{
		if( $identify = ImageGuru::doIdentify($filename) )
		{
			print_r($identify);
			if( $identify['format'] == 'PNG' ) return true;
		}
		return false;
	}


	public function isInputValid()
	{
		foreach($this->inputFiles as $file)
		{
			if( !self::isValidFile($file) ) return false;
		}
		return true;
	}

	public function writeFile($outFile, Array $options = array())
	{
		$tempFile = $this->getTempFile();

		if( count($options) == 0 )
		{
			foreach($this->options as $name => $value)
			{
				$options[] = sprintf('-%s %s', $name, escapeshellarg($value));
			}
		}

		$inputFile = reset($this->inputFiles);

		if( self::doConvert($inputFile, $tempFile, $options) && rename($tempFile, $outFile) )
		{
			chmod($outFile, FILE_CREATE_PERMS);
			return true;
		}

		if( file_exists($tempFile) ) unlink($tempFile);
		return false;
	}
}
<?
class FileException extends Exception
{}

class File
{
	public function download($fromURL, $toFile = null)
	{
		$this->bytes = 0;

		try
		{
			if( empty($fromURL) )
				throw New FileException('URL empty');

			if( !$toFile )
				$toFile = tempnam(DIR_TEMP, 'AsenineDownload');

			if( !$d = @fopen($toFile, 'w') )
				throw New FileException(sprintf('Could not open destination "%s" for writing', $toFile));

			if( !$s = @fopen($fromURL, 'r') )
				throw New FileException(sprintf('Could not open source "%s" for reading', $fromURL));

			while(($buffer = fgets($s, 4096)) !== false)
				$this->bytes += fputs($d, $buffer);

			fclose($s);
			fclose($d);

			return $toFile;
		}
		catch(Exception $e)
		{
			if( $d ) fclose($d);
			if( $s ) fclose($s);

			throw $e;
		}
	}
}
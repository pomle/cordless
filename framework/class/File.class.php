<?
class FileException extends Exception
{}

class File
{
	protected
		$location,
		$size,
		$hash,
		$name;

	public
		$mime;


	public static function fromURL($fromURL, $toFile = null)
	{
		$d = $s = null;

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

			$bufferSize = 512 * 16;

			$t = microtime(true);

			$downloadBytes = 0;

			while(($buffer = fgets($s, $bufferSize)) !== false)
				$downloadBytes += fputs($d, $buffer);

			$downloadTime = microtime(true) - $t;

			fclose($s);
			fclose($d);


			$name = basename($fromURL);
			if( strpos($name, '%') !== false ) $name = urldecode($name); ### If URL contains % we assume it's URL encoded.


			$File = new self($toFile, filesize($toFile));

			$File->name = $name;

			$File->downloadBytes = $downloadBytes;
			$File->downloadTime = $downloadTime;

			return $File;
		}
		catch(Exception $e)
		{
			if( $d ) fclose($d);
			if( $s ) fclose($s);

			throw $e;
		}
	}

	public static function fromPHPUpload($phpfile)
	{
		$File = new self($phpfile['tmp_name'], $phpfile['size'], $phpfile['type']);
		$File->name = $phpfile['name'];
		return $File;
	}


	public function __construct($location, $size = null, $mime = null, $name = null)
	{
		if( !is_string($location) )
			trigger_error(__METHOD__ . ' expects arg #1 to be string, ' . gettype($location) . ' given', E_USER_WARNING);

		$location = (string)$location;

		if( !file_exists($location) )
			throw New FileException(sprintf("Path does not exist: %s", $location));

		if( !is_file($location) )
			throw New FileException(sprintf("Path is not a file: %s", $location));

		$this->location = $location;

		### File size can only be integer and must not be negative
		if( !is_null($size) && ( !is_int($size) && ( $size < 0 ) ) )
			throw New FileException(sprintf("File must be integer and 0 or more"));

		$this->size = $size;
		$this->mime = $mime;
		$this->name = $name ?: basename($this->location);
	}

	public function __get($key)
	{
		### Auto calculate hash and size if not available already
		switch($key)
		{
			case 'hash':
				if( is_null($this->hash) )
					$this->hash = md5_file($this->location);

				return $this->hash;
			break;

			case 'size':
				if( is_null($this->size) )
					$this->size = filesize($this->location);

				return $this->size;
			break;
		}

		return $this->$key;
	}

	public function __isset($key)
	{
		return isset($this->$key);
	}

	public function __toString()
	{
		return $this->location;
	}


	public function copy($to)
	{
		if( !copy($this->location, $to) )
			throw New FileException(sprintf("File copy from %s to %s failed", $this->location, $to));

		$File_New = clone $this;
		$File_New->location = $to;

		return $File_New;
	}

	public function move($to)
	{
		if( !rename($this->location, $to) )
			throw New FileException(sprintf("File move from %s to %s failed", $this->location, $to));

		$this->location = $to;

		return true;
	}

	public function delete()
	{
		if( !unlink($this->location) )
			throw New FileException(sprintf("File delete at %s failed", $this->location));

		return true;
	}

	public function isExisting()
	{
		return file_exists($this->location) && is_file($this->location);
	}

	public function isReadable()
	{
		return is_readable($this->location);
	}

	public function isWriteable()
	{
		return is_readable($this->location);
	}
}
<?
interface iMedia
{
	public static function canHandleFile($filePath);
	public function __construct($mediaHash = null, \File $File = null);
	public function getInfo();
}

abstract class Media implements iMedia
{
	const PATH_DEPTH = 5;

	protected
		$File;

	public
		$mediaHash,
		$mimeType,
		$fileOriginalName;



	public static function createFromFile(\File $File)
	{
		if( !$File->isReadable() )
		{
			trigger_error("File not readable: \"$filePath\"", E_USER_WARNING);
			return false;
		}

		if( !static::canHandleFile($File) )
		{
			trigger_error(get_called_class() . " does not handle file: \"$filePath\"", E_USER_WARNING);
			return false;
		}

		$mediaHash = $File->hash;

		$Media = new static($mediaHash, $File);
		$Media->mimeType = $File->mime;

		return $Media;
	}

	public static function createFromFilename($filename, $mime = null)
	{
		return self::createFromFile( new \File($filename, null, null, $mime) );
	}

	public static function createFromHash($mediaHash)
	{
		$filePath = DIR_MEDIA_SOURCE . $mediaHash;
		return new static($mediaHash, $filePath);
	}


	public function __construct($mediaHash = null, \File $File = null)
	{
		#if( strlen($mediaHash) !== 32 ) trigger_error(__METHOD__ . ' expects argument 1 to be string of exact length 32', E_USER_ERROR);
		$this->mediaHash = $mediaHash;
		$this->File = $File;
	}

	final public function __toString()
	{
		return $this->mediaHash;
	}


	final public function getFilePath()
	{
		return (string)$this->File; ### $File::__toString() provides $File->location and if null will be ""
	}

	final public function getFileOriginalName()
	{
		if( !isset($this->mediaID) )
			return false;

		if( !isset($this->fileOriginalName) )
			$this->fileOriginalName = \Manager\Dataset\Media::getFileOriginalName($this->mediaID) ?: sprintf('Media_%u.unknownExt', $this->mediaID);

		return $this->fileOriginalName;
	}

	final public function isFileValid()
	{
		return static::canHandleFile($this->filePath);
	}
}
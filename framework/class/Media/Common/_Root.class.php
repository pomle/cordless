<?
namespace Media\Common;

interface _Interface
{
	public static function canHandleFile($filePath);
	public function getInfo();
}

abstract class _Root implements _Interface
{
	const PATH_DEPTH = 5;

	public
		$mediaHash,
		$filePath;


	public static function createFromFile($filePath)
	{
		if( !file_exists($filePath) )
		{
			trigger_error("Path does not exist: \"$filePath\"", E_USER_WARNING);
			return false;
		}

		if( !is_file($filePath) )
		{
			trigger_error("Path is not a file: \"$filePath\"", E_USER_WARNING);
			return false;
		}

		if( !is_readable($filePath) )
		{
			trigger_error("File not readable: \"$filePath\"", E_USER_WARNING);
			return false;
		}

		if( !static::canHandleFile($filePath) )
		{
			trigger_error(get_called_class() . " does not handle file: \"$filePath\"", E_USER_WARNING);
			return false;
		}

		$mediaHash = md5_file($filePath);

		return new static($mediaHash, $filePath);
	}

	public static function createFromHash($mediaHash)
	{
		$filePath = DIR_MEDIA_SOURCE . $mediaHash;
		return new static($mediaHash, $filePath);
	}


	final public function __construct($mediaHash, $filePath)
	{
		if( strlen($mediaHash) !== 32 ) trigger_error(__METHOD__ . ' expectes argument 1 to be string of exact length 32', E_USER_ERROR);
		$this->mediaHash = $mediaHash;
		$this->filePath = $filePath;
	}

	public function __toString()
	{
		return $this->mediaHash;
	}


	final public function getFilePath()
	{
		return $this->filePath;
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
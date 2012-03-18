<?
namespace Asenine;

use \Asenine\DB as DB;

class MediaException extends \Exception
{}

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
		$mediaID,
		$mediaHash,
		$mimeType,
		$fileOriginalName;



	public static function createFromFile(File $File)
	{
		if( !$File->isReadable() )
		{
			trigger_error("File not readable: \"" . $File . "\"", E_USER_WARNING);
			return false;
		}

		if( !static::canHandleFile($File) )
		{
			trigger_error(get_called_class() . " can not handle file: \"" . $File . "\"", E_USER_WARNING);
			return false;
		}

		$mediaHash = $File->hash;

		$Media = new static($mediaHash, $File);
		$Media->fileOriginalName = $File->name;
		$Media->mimeType = $File->mime;

		return $Media;
	}

	final public static function createFromFilename($filename, $mime = null)
	{
		return self::createFromFile( new File($filename, null, null, $mime) );
	}

	final public static function createFromHash($mediaHash)
	{
		$filePath = DIR_MEDIA_SOURCE . $mediaHash;
		return new static($mediaHash, new File($filePath) );
	}

	public static function createFromType($type, $mediaHash, File $File)
	{
		if( strlen($type) == 0 )
		{
			#trigger_error(__METHOD__ . ' requires argument #1 to be non-zero length string', E_USER_WARNING);
			return false;
		}

		$classPath = '\\Asenine\\Media\\Type\\' . ucfirst($type);

		if( class_exists($classPath) )
			return new $classPath($mediaHash, $File);

		return false;
	}

	public static function integrateIntoLibrary(self $Media)
	{
		$inputFile = $Media->getFilePath();

		if( !file_exists($inputFile) )
			throw new MediaException("Could not find file \"$inputFile\"");

		if( !is_file($inputFile) )
			throw new MediaException("\"$inputFile\" is not a valid file");

		if( !is_readable($inputFile) )
			throw new MediaException("Could not read file \"$inputFile\"");


		if( !file_exists(DIR_MEDIA_SOURCE) && !@mkdir(DIR_MEDIA_SOURCE, 0775, true) )
			throw new MediaException("Could not create dir: \"" . DIR_MEDIA_SOURCE . "\"");


		$fileHash = md5_file($inputFile);
		$libraryFile = DIR_MEDIA_SOURCE . $fileHash;

		if( !file_exists($libraryFile) && !@copy($inputFile, $libraryFile) )
			throw new MediaException("Could not write to source library path \"$libraryFile\"");

		$query = DB::prepareQuery("SELECT ID FROM Asenine_Media WHERE fileHash = %s", $fileHash);
		$mediaID = DB::queryAndFetchOne($query);

		if( !$mediaID || isset($Media->mediaID) )
		{
			$query = DB::prepareQuery("INSERT INTO
				Asenine_Media
				(
					ID,
					timeCreated,
					timeModified,
					mediaType,
					fileHash,
					fileSize,
					fileOriginalName,
					fileMimeType
				) VALUES(
					NULLIF(%u, 0),
					UNIX_TIMESTAMP(),
					UNIX_TIMESTAMP(),
					%s,
					%s,
					NULLIF(%u, 0),
					NULLIF(%s, ''),
					NULLIF(%s, '')
				) ON DUPLICATE KEY UPDATE
					timeModified = VALUES(timeModified),
					mediaType = VALUES(mediaType),
					fileHash = VALUES(fileHash),
					fileSize = VALUES(fileSize),
					fileOriginalName = VALUES(fileOriginalName),
					fileMimeType = VALUES(fileMimeType)",
				isset($Media->mediaID) ? $Media->mediaID : 0,
				$Media::TYPE,
				$fileHash,
				filesize($libraryFile),
				$Media->fileOriginalName,
				$Media->mimeType);

			$mediaID = DB::queryAndGetID($query);
		}

		return self::loadFromDB($mediaID);
	}

	public static function loadByHash($mediaHash)
	{
		return static::loadFromDB(Media\Dataset::getIDFromHash($mediaHash));
	}

	public static function loadFromDB($mediaIDs)
	{
		if( !$returnArray = is_array($mediaIDs) )
			$mediaIDs = (array)$mediaIDs;

		$medias = array_fill_keys($mediaIDs, false);

		$query = DB::prepareQuery("SELECT
				m.ID AS mediaID,
				m.mediaType,
				m.fileHash AS mediaHash,
				m.fileSize,
				m.fileOriginalName,
				m.fileMimeType
			FROM
				Asenine_Media m
			WHERE
				m.ID IN %a",
			$mediaIDs);

		$result = DB::queryAndFetchResult($query);

		while($media = DB::assoc($result))
		{
			$mediaID = (int)$media['mediaID'];

			try
			{
				$File = new File(
					DIR_MEDIA_SOURCE . $media['mediaHash'],
					(int)$media['fileSize'] ?: null,
					$media['fileMimeType'],
					$media['fileOriginalName']);

				if( !$Media = self::createFromType($media['mediaType'], $media['mediaHash'], $File) )
					$Media = new \Asenine\Media\Type\Defunct($media['mediaHash'], $File); ### Fallback to Defunct type

				$Media->fileOriginalName = $media['fileOriginalName'];
				$Media->mimeType = $media['fileMimeType'];
				$Media->mediaID = $mediaID;

				$medias[$Media->mediaID] = $Media;
			}
			catch(\Exception $e)
			{
				if( DEBUG )
					trigger_error(sprintf("Could not instantiate Media with ID %d, %s", $mediaID, $e->getMessage()), E_USER_WARNING);
			}
		}

		$medias = array_filter($medias);

		return $returnArray ? $medias : reset($medias);
	}

	public static function removeFromDB($mediaID, $forceDBDelete = false)
	{
		$skipSourceDelete = false;
		$skipDBDelete = false;

		if( !$Media = self::loadFromDB($mediaID) )
		{
			trigger_error("Media ID not in database: \"$mediaID\"", E_USER_NOTICE);
			return false;
		}

		### Collect all autogenerated material and delete it
		$files = Media\Dataset::getSpreadByHash($Media->mediaHash);
		foreach($files as $file)
		{
			if( is_file($file) || !is_writable($file) || !unlink($file) )
			{
				$skipSourceDelete = true;
				trigger_error("File \"$file\" was found but could not be removed", E_USER_WARNING);
			}
		}

		### Only remove source file if all autogenerated files could be deleted
		if( $skipSourceDelete === false )
		{
			$sourceFile = $Media->getFilePath();
			if( file_exists($sourceFile) && ( !is_writable($sourceFile) || !unlink($sourceFile) ) )
			{
				trigger_error("Source file \"$sourceFile\" was found but could not be removed", E_USER_WARNING);

				### Only delete DB row if source file could be deleted to avoid stray files
				$skipDBDelete = true;
			}
		}

		### Notice that DB skip can be overridden
		if( $skipDBDelete === false || $forceDBDelete === true )
		{
			$query = DB::prepareQuery("DELETE FROM Asenine_Media WHERE ID = %u", $Media->mediaID);
			DB::queryAndCountAffected($query);

			return true;
		}

		return false;
	}


	public function __construct($mediaHash = null, File $File = null)
	{
		#if( strlen($mediaHash) !== 32 ) trigger_error(__METHOD__ . ' expects argument 1 to be string of exact length 32', E_USER_ERROR);
		$this->mediaHash = $mediaHash;
		$this->File = $File;
	}

	final public function __get($key)
	{
		return $this->$key;
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
			$this->fileOriginalName = Media\Dataset::getFileOriginalName($this->mediaID) ?: sprintf('Media_%u.unknownExt', $this->mediaID);

		return $this->fileOriginalName;
	}

	final public function isFileValid()
	{
		return static::canHandleFile($this->filePath);
	}
}
<?
namespace Manager;

class Media extends Common\DB
{
	const ERROR_FILE_NOT_FOUND = 1;
	const ERROR_FILE_NOT_READABLE = 2;
	const ERROR_FILE_NOT_FILE = 3;
	const ERROR_FILE_COPY_FAILED = 4;

	public static function createFromFile($inputFile)
	{
		// May return several "hits", if $inputFile can be handled by multiple plugins

		$medias = array();

		$plugins = Dataset\Media::getPlugins();

		foreach($plugins as $className)
			if( $className::canHandleFile($inputFile) )
				$medias[] = $className::createFromFile($inputFile);

		return $medias;
	}

	public static function createFromType($type, $mediaHash, $filePath)
	{
		if( strlen($type) == 0 )
		{
			#trigger_error(__METHOD__ . ' requires argument #1 to be non-zero length string', E_USER_WARNING);
			return false;
		}

		$classPath = '\\Media\\' . ucfirst($type);

		if( class_exists($classPath) )
			return new $classPath($mediaHash, $filePath);

		return false;
	}


	public static function integrateIntoLibrary(\Media $Media, $originalFileName = null)
	{
		$inputFile = $Media->getFilePath();

		if( !file_exists($inputFile) )
		{
			trigger_error("Could not find file \"$inputFile\"", E_USER_WARNING);
			return false;
		}

		if( !is_file($inputFile) )
		{
			trigger_error("\"$inputFile\" is not a valid file", E_USER_WARNING);
			return false;
		}

		if( !is_readable($inputFile) )
		{
			trigger_error("Could not read file \"$inputFile\"", E_USER_WARNING);
			return false;
		}

		$fileHash = md5_file($inputFile);
		$libraryFile = DIR_MEDIA_SOURCE . $fileHash;

		if( !file_exists($libraryFile) && !@copy($inputFile, $libraryFile) )
		{
			trigger_error("Could not write to source library path \"$libraryFile\"", E_USER_WARNING);
			return false;
		}

		$query = \DB::prepareQuery("SELECT ID FROM Media WHERE fileHash = %s", $fileHash);
		$mediaID = \DB::queryAndFetchOne($query);

		if( !$mediaID )
		{
			$query = \DB::prepareQuery("INSERT INTO
				Media
				(
					timeCreated,
					fileHash,
					fileSize,
					fileOriginalName,
					mediaType
				) VALUES(
					UNIX_TIMESTAMP(),
					%s,
					NULLIF(%u, 0),
					NULLIF(%s, ''),
					%s)",
				$fileHash,
				filesize($libraryFile),
				$originalFileName,
				$Media::TYPE);

			$mediaID = \DB::queryAndGetID($query);
		}

		return self::loadOneFromDB($mediaID);
	}

	public static function loadByHash($mediaHash)
	{
		return static::loadOneFromDB(Dataset\Media::getIDFromHash($mediaHash));
	}

	public static function loadFromDB($mediaIDs)
	{
		$medias = array_fill_keys($mediaIDs, false);

		$query = \DB::prepareQuery("SELECT
				m.ID AS mediaID,
				m.fileHash AS mediaHash,
				m.mediaType
			FROM
				Media m
			WHERE
				m.ID IN %a",
			$mediaIDs);

		$result = \DB::queryAndFetchResult($query);

		while($media = \DB::assoc($result))
		{
			$Media = self::createFromType($media['mediaType'], $media['mediaHash'], DIR_MEDIA_SOURCE . $media['mediaHash']);

			### Fallback to Defunct type
			if( !$Media )
				$Media = new \Media\Defunct($media['mediaHash'], DIR_MEDIA_SOURCE . $media['mediaHash']);


			$Media->mediaID = (int)$media['mediaID'];

			$medias[$Media->mediaID] = $Media;
		}

		$medias = array_filter($medias);

		return $medias;
	}

	public static function removeFromDB($mediaID, $forceDBDelete = false)
	{
		$skipSourceDelete = false;
		$skipDBDelete = false;

		if( !$Media = self::loadOneFromDB($mediaID) )
		{
			trigger_error("Media ID not in database: \"$mediaID\"", E_USER_NOTICE);
			return false;
		}

		### Collect all autogenerated material and delete it
		$files = \Manager\Dataset\Media::getSpreadByHash($Media->mediaHash);
		foreach($files as $file)
		{
			if( !is_writable($file) || !unlink($file) )
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
			$query = \DB::prepareQuery("DELETE FROM Media WHERE ID = %u", $Media->mediaID);
			\DB::queryAndCountAffected($query);

			return true;
		}

		return false;
	}
}
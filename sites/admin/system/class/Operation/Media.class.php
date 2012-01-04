<?
namespace Operation;

class Media
{
	public static function createFromFile($filepath, $preferredMediaType = null)
	{
		if( empty($filepath) )
			throw New \Exception('Filepath empty');

		if( !is_file($filepath) )
			throw New \Exception('Not a valid file');

		if( !is_file($filepath) )
			throw New \Exception('File not readable');

		$media = \Manager\Media::createFromFile($filepath);

		### No plug-ins accepted file
		if( count($media) == 0 )
			throw New \Exception('File not supported by available Media Plugins');

		### If only handled by one plug-in, return this
		if( count($media) == 1 )
			return reset($media);


		if( count($media) > 1 )
		{
			foreach($media as $Media)
				$mediaTypes[$Media::TYPE] = '"' . $Media::DESCRIPTION . '"';

			$mediaDesc = \Manager\Dataset\Media::getDescriptionByType($preferredMediaType);

			### Return list of plug-ins that identified media
			if( !is_null($preferredMediaType) )
			{
				foreach($media as $Media)
					if( $preferredMediaType == $Media::TYPE ) return $Media;

				throw New \Exception('Could not import file as "' . $preferredMediaDesc . '" since it was not an alternative. Supported types are ' . join(', ', $mediaTypes));
			}

			throw New \Exception('Media type is ambigious, can be any of ' . join(', ', $mediaTypes));
		}
	}

	public static function downloadFileToLibrary($url, $preferredMediaType = null)
	{
		$name = basename($url);

		if( strpos($name, '%') ) $name = urldecode($name); ### If URL contains % we assume it's URL encoded.

		$FileOp = new \File();

		if( !$downloadedFile = $FileOp->download($url) )
			throw New \Exception('Download Failed');

		\Message::addNotice(sprintf("Read %u bytes from %s", $FileOp->bytes, $url));

		$Media = \Operation\Media::importFileToLibrary($downloadedFile, $name, $preferredMediaType);

		return $Media;
	}

	public static function importFileToLibrary($filepath, $originalFilename = null, $preferredMediaType = null, $requireType = null)
	{
		### Create Media Object from File
		$Media_New = self::createFromFile($filepath, $preferredMediaType);

		if( $requireType && $requireType !== $Media_New::TYPE )
		{
			$mediaDesc = \Manager\Dataset\Media::getDescriptionByType($requireType);
			throw New \Exception(sprintf('Endast media av typen "%s" kan importeras', $mediaDesc));
		}


		if( $preferredMediaType && ($Media_Existing = \Manager\Media::loadByHash($Media_New->mediaHash)) )
		{
			if( $preferredMediaType !== $Media_Existing::TYPE )
			{
				$mediaDesc = \Manager\Dataset\Media::getDescriptionByType($preferredMediaType);
				throw New \Exception('Media already exists in database as "' . $Media_Existing::DESCRIPTION . '" and can not be imported as "' . $mediaDesc . '"');
			}
		}

		### Extend Object as Integrated
		$Media_New = \Manager\Media::integrateIntoLibrary($Media_New, $originalFilename);

		return $Media_New;
	}

	public static function importToProduct(\Media\Common\_Root $Media, $productID, $localeID = null)
	{
		$query = \DB::prepareQuery("INSERT IGNORE INTO
			ProductMedia (
				productID,
				mediaID,
				isEnabled,
				sortOrder
			) VALUES(
				%u,
				%u,
				1,
				UNIX_TIMESTAMP()
			)",
			$productID,
			$Media->mediaID);

		\DB::queryAndGetID($query);

		if( $localeID )
		{
			$query = \DB::prepareQuery("INSERT IGNORE INTO
				ProductMediaLocale (
					productID,
					mediaID,
					localeID,
					isEnabled
				) VALUES(
					%u,
					%u,
					%u,
					%u)",
				$productID,
				$Media->mediaID,
				$localeID,
				$isEnabled = 1);

			\DB::queryAndGetID($query);
		}

		return true;
	}

	public static function receiveUpload($file)
	{
		### $file is an item from php's $_FILES-array

		global $User;
		ensurePolicies('AllowCreateMedia');

		return self::importFileToLibrary($file['tmp_name'], $file['name']);
	}
}
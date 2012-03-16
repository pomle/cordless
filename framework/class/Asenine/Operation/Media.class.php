<?
namespace Asenine\Operation;

class Media
{
	public static function createFromFile(\File $File, $preferredMediaType = null)
	{
		$medias = \Manager\Media::createFromFile($File);

		### No plug-ins accepted file
		if( count($medias) == 0 )
			throw New \Exception('File not supported by available Media Plugins');

		### If only handled by one plug-in, return instance of it
		if( count($medias) == 1 )
			return reset($medias);

		if( count($medias) > 1 )
		{
			foreach($medias as $Media)
				$mediaTypes[$Media::TYPE] = '"' . $Media::DESCRIPTION . '"';

			$mediaDesc = \Manager\Dataset\Media::getDescriptionByType($preferredMediaType);

			### Return list of plug-ins that identified media
			if( !is_null($preferredMediaType) )
			{
				foreach($medias as $Media)
					if( $preferredMediaType == $Media::TYPE ) return $Media;

				throw New \Exception('Could not import file as "' . $preferredMediaDesc . '" since it was not an alternative. Supported types are ' . join(', ', $mediaTypes));
			}

			throw New \Exception('Media type is ambigious, can be any of ' . join(', ', $mediaTypes));
		}
	}

	public static function downloadFileToLibrary($url, $preferredMediaType = null, $mediaID = null)
	{
		try
		{
			$File = \File::fromURL($url);

			$Media = \Operation\Media::importFileToLibrary($File, $File->name, $preferredMediaType, null, $mediaID);

			$File->delete();

			return $Media;
		}
		catch(\Exception $e)
		{
			if( isset($downloadedFile) && file_exists($downloadedFile) ) unlink($downloadedFile);

			throw $e;
		}
	}

	public static function importFileToLibrary(\File $File, $originalFilename = null, $preferredMediaType = null, $requireType = null, $mediaID = null)
	{

		### Create Media Object from File
		$Media_New = self::createFromFile($File, $preferredMediaType);

		if( $requireType && $requireType !== $Media_New::TYPE )
		{
			$mediaDesc = \Manager\Dataset\Media::getDescriptionByType($requireType);
			throw New \Exception(sprintf('Only media of type "%s" can be importerd', $mediaDesc));
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
		$Media_New->fileOriginalName = $originalFilename;
		$Media_New->mediaID = $mediaID;
		$Media_New = \Manager\Media::integrateIntoLibrary($Media_New);

		return $Media_New;
	}
}
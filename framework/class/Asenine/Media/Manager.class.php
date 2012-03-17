<?
namespace Asenine\Media;

class Manager
{
	public static function createFromFile(\Asenine\File $File)
	{
		// May return several "hits", if $File can be handled by multiple plugins

		$medias = array();

		$plugins = Dataset::getPlugins();

		foreach($plugins as $className)
			if( $className::canHandleFile($File) )
				$medias[] = $className::createFromFile($File);

		return $medias;
	}

	public static function createFromFilename($filename, $mime = null)
	{
		return self::createFromFile( new \Asenine\File($filename, null, null, $mime) );
	}
}
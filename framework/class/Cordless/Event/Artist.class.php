<?
namespace Cordless\Event;

class Artist
{
	public static function createFromName($name)
	{
		if( !$Artist = reset(\Cordless\Artist::loadByName($name)) )
		{
			$Artist = new \Cordless\Artist($name);

			if( $Image = Artwork::createFromArtist($Artist) )
				$Artist->setImage($Image);
		}

		return $Artist;
	}
}
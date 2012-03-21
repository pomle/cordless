<?
namespace Cordless\Event;

class Artwork
{
	public static function createFromArtist(\Cordless\Artist $Artist)
	{
		if( $LastFM = \Cordless\getLastFM() )
			if( $Image = $LastFM->getArtistImage($Artist->name) )
				return $Image;

		return false;
	}
}
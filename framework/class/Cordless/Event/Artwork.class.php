<?
namespace Cordless\Event;

class Artwork
{
	public static function createFromArtist(\Cordless\Artist $Artist)
	{
		$LastFM = \Cordless\getLastFM();

		$Image = $LastFM->getArtistImage($Artist->name);

		return $Image;
	}
}
<?
namespace Cordless\Event;

class Album
{
	public static function createFromInfo($title, $year = null)
	{
		if( !$Album = \Cordless\Album::loadByTitle($title) )
		{
			$Album = new \Cordless\Album($title, mktime(0, 0, 0, 1, 1, (int)$year));
		}

		return $Album;
	}
}
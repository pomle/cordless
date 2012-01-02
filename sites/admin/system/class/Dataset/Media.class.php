<?
namespace Dataset;

class Media
{
	public static function getTypeMap()
	{
		return array(
			'default' => array('icon' => 'help', 'caption' => _('Ogiltig mediatyp')),
			'audio' => array('icon' => 'sound', 'caption' => _('Ljud')),
			'image' => array('icon' => 'picture', 'caption' => _('Bild')),
			'video' => array('icon' => 'film', 'caption' => _('Video')),
			'rotate' => array('icon' => 'rotate', 'caption' => _('Roterbar'))
		);
	}
}
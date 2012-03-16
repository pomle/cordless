<?
namespace Dataset;

class Media
{
	public static function getTypeMap()
	{
		return array(
			'default' => array('icon' => 'help', 'caption' => _('Invalid')),
			'audio' => array('icon' => 'sound', 'caption' => _('Audio')),
			'image' => array('icon' => 'picture', 'caption' => _('Image')),
			'video' => array('icon' => 'film', 'caption' => _('Video')),
			'rotate' => array('icon' => 'rotate', 'caption' => _('Rotatable'))
		);
	}
}
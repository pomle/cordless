<?
namespace Dataset;

class User
{
	public static function getSecurityIPTypeMap()
	{
		$map = array
		(
			'allow' => array('icon' => 'accept', 'caption' => _('Tillåt')),
			'deny' => array('icon' => 'cancel', 'caption' => _('Avvisa'))
		);

		return $map;
	}
}
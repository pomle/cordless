<?
namespace Asenine\Manager\Dataset;

class UserSetting
{
	public static function getAvailable()
	{
		$userSettings = array();
		include ASENINE_DIR_CONFIG . 'UserSettings.inc.php';
		return $userSettings;
	}
}
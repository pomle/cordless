<?
namespace Manager\Dataset;

class UserSetting
{
	public static function getAvailable()
	{
		$userSettings = array();
		include DIR_ASENINE_CONFIG . 'UserSettings.inc.php';
		return $userSettings;
	}
}
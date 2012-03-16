<?
namespace Asenine\User;

use
	\Asenine\User,
	\Asenine\DB;

class Operation
{
	public static function setPasswordAsUser($userID, $currentPassword, $newPassword, $newPasswordVerify)
	{
		self::verifyPassword($newPassword, $newPasswordVerify);

		if( !$crypto = Manager::getPasswordCrypto($userID) )
			throw New \Exception(_('Ogiltigt användare'));

		$currentPasswordHash = User::createHash($currentPassword, $crypto);
		$newPasswordHash = User::createHash($newPassword, $crypto);

		$query = DB::prepareQuery("SELECT COUNT(*) FROM Users WHERE ID = %u AND passwordHash = %s", $userID, $currentPasswordHash);
		$res = (int)DB::queryAndFetchOne($query);

		if( $res !== 1 )
			throw new \Exception(_('Nuvarande lösenord matchar inte'));

		return Manager::setPassword($userID, $newPassword);
	}

	public static function verifyPassword($newPassword, $newPasswordVerify)
	{
		if( strlen($newPassword) < User::PASSWORD_MIN_LEN)
			throw new \Exception(sprintf(_('Lösenord för kort. Lösenord måste bestå av minst %u tecken.'), User::PASSWORD_MIN_LEN));

		if( $newPassword !== $newPasswordVerify )
			throw new \Exception(_('Lösenorden matchar inte'));
	}

	public static function verifyUsername($username, $discountUserID = 0)
	{
		$usernameLen = mb_strlen($username);
		$minLen = User::USERNAME_MIN_LEN;
		$maxLen = User::USERNAME_MAX_LEN;

		if( (isset($minLen) && $usernameLen < $minLen) || (isset($maxLen) && $usernameLen > $maxLen) )
			throw New \Exception(sprintf(_('Användarnamnet har ogiltig längd och måste bestå av minst %u och mest %u tecken'), $minLen, $maxLen));

		$query = DB::prepareQuery("SELECT COUNT(*) FROM Asenine_Users WHERE username = %s AND NOT ID = %u", $username, $discountUserID);
		if( (bool)DB::queryAndFetchOne($query) )
			throw New \Exception(sprintf(_('Användarnamnet "%s" är upptaget'), $username));
	}
}
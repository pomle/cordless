<?
namespace Asenine\User;

use \Asenine\DB as DB;

class Dataset
{
	public static function getAll()
	{
		$query = "SELECT ID FROM Asenine_Users";
		$userIDs = DB::queryAndFetchArray($query);
		return self::getNames($userIDs);
	}

	public static function getEmail($userID)
	{
		$query = DB::prepareQuery("SELECT email FROM Asenine_Users WHERE ID = %u", $userID);
		return DB::queryAndFetchOne($query);
	}

	public static function getFullname($userID)
	{
		$query = DB::prepareQuery("SELECT fullname FROM Asenine_Users WHERE ID = %u", $userID);
		return DB::queryAndFetchOne($query);
	}

	public static function getGroups($userID)
	{
		$query = DB::prepareQuery("SELECT userGroupID FROM Asenine_UserGroupUsers ugu WHERE ugu.userID = %u", $userID);
		return DB::queryAndFetchArray($query);
	}

	public static function getNames($userIDs)
	{
		$query = DB::prepareQuery("SELECT ID, username, IFNULL(NULLIF(fullname, ''), username) AS fullname FROM Asenine_Users WHERE ID IN %a ORDER BY username ASC", $userIDs);
		return DB::queryAndFetchArray($query);
	}

	public static function getPasswordCrypto($userID)
	{
		$query = DB::prepareQuery("SELECT passwordCrypto FROM Asenine_Users WHERE ID = %u", $userID);
		$passwordCrypto = DB::queryAndFetchOne($query);
		return $passwordCrypto;
	}

	public static function getPolicies($userID)
	{
		$query = DB::prepareQuery("SELECT policyID FROM Asenine_UserPolicies WHERE userID = %u", $userID);
		return DB::queryAndFetchArray($query);
	}

	public static function getProperties($userID)
	{
		$query = DB::prepareQuery("SELECT
				ID AS userID,
				isEnabled,
				isAdministrator,
				timeCreated,
				timePasswordLastChange,
				timeLastLogin,
				countLoginsSuccessful,
				countLoginsFailed,
				username,
				fullname,
				email,
				phone,
				timeAutoLogout
			FROM
				Asenine_Users
			WHERE
			ID = %u",
			$userID);

		return DB::queryAndFetchOne($query);
	}

	public static function getUserID($username)
	{
		$query = DB::prepareQuery("SELECT userID FROM Asenine_Users WHERE username = %s LIMIT 1", $username);
		$userID = (int)DB::queryAndFetchOne($query);
		return $userID;
	}

	public static function getUsername($userID)
	{
		$query = DB::prepareQuery("SELECT username FROM Asenine_Users WHERE ID = %u", $userID);
		return DB::queryAndFetchOne($query);
	}

	public static function isAdministrator($userID)
	{
		$query = DB::prepareQuery("SELECT isAdministrator FROM Asenine_Users WHERE ID = %u", $userID);
		$isAdministrator = (bool)DB::queryAndFetchOne($query);
		return $isAdministrator;
	}

	public static function isEnabled($userID)
	{
		$query = DB::prepareQuery("SELECT isEnabled FROM Asenine_Users WHERE ID = %u", $userID);
		$isEnabled = (bool)DB::queryAndFetchOne($query);
		return $isEnabled;
	}
}
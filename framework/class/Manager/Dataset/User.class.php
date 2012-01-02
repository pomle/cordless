<?
namespace Manager\Dataset;

class User extends _Common
{
	const TABLE = 'Users';

	public static function getAll()
	{
		$query = "SELECT ID FROM Users";
		$userIDs = \DB::queryAndFetchArray($query);
		return self::getNames($userIDs);
	}

	public static function getEmail($userID)
	{
		$query = \DB::prepareQuery("SELECT email FROM Users WHERE ID = %u", $userID);
		return \DB::queryAndFetchOne($query);
	}

	public static function getFullname($userID)
	{
		$query = \DB::prepareQuery("SELECT fullname FROM Users WHERE ID = %u", $userID);
		return \DB::queryAndFetchOne($query);
	}

	public static function getGroups($userID)
	{
		$query = \DB::prepareQuery("SELECT userGroupID FROM UserGroupUsers ugu WHERE ugu.userID = %u", $userID);
		return \DB::queryAndFetchArray($query);
	}

	public static function getNames($userIDs)
	{
		$query = \DB::prepareQuery("SELECT ID, username, IFNULL(NULLIF(fullname, ''), username) AS fullname FROM Users WHERE ID IN %a ORDER BY username ASC", $userIDs);
		return \DB::queryAndFetchArray($query);
	}

	public static function getPasswordCrypto($userID)
	{
		$query = \DB::prepareQuery("SELECT passwordCrypto FROM Users WHERE ID = %u", $userID);
		$passwordCrypto = \DB::queryAndFetchOne($query);
		return $passwordCrypto;
	}

	public static function getPolicies($userID)
	{
		$query = \DB::prepareQuery("SELECT policyID FROM UserPolicies WHERE userID = %u", $userID);
		return \DB::queryAndFetchArray($query);
	}

	public static function getProperties($userID)
	{
		$query = \DB::prepareQuery("SELECT
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
				Users
			WHERE
			ID = %u",
			$userID);

		return \DB::queryAndFetchOne($query);
	}

	public static function getUserID($username)
	{
		$query = \DB::prepareQuery("SELECT userID FROM Users WHERE username = %s LIMIT 1", $username);
		$userID = (int)\DB::queryAndFetchOne($query);
		return $userID;
	}

	public static function getUsername($userID)
	{
		$query = \DB::prepareQuery("SELECT username FROM Users WHERE ID = %u", $userID);
		return \DB::queryAndFetchOne($query);
	}

	public static function isAdministrator($userID)
	{
		$query = \DB::prepareQuery("SELECT isAdministrator FROM Users WHERE ID = %u", $userID);
		$isAdministrator = (bool)\DB::queryAndFetchOne($query);
		return $isAdministrator;
	}

	public static function isEnabled($userID)
	{
		$query = \DB::prepareQuery("SELECT isEnabled FROM Users WHERE ID = %u", $userID);
		$isEnabled = (bool)\DB::queryAndFetchOne($query);
		return $isEnabled;
	}
}
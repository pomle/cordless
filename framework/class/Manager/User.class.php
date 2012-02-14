<?
namespace Manager;

class User extends Common\DB
{
	public static function addToDB()
	{
		$query = \DB::prepareQuery("INSERT INTO Users (ID, isEnabled, timeCreated, username, passwordCrypto) VALUES(NULL, 0, UNIX_TIMESTAMP(), NULL, %s)", \User::createCrypto());
		$userID = (int)\DB::queryAndGetID($query);
		return $userID;
	}

	public static function addPolicy($userID, $policyID)
	{
		$query = \DB::prepareQuery("REPLACE INTO UserPolicies (userID, policyID) VALUES(%u, %u)", $userID, $policyID);
		return \DB::queryAndCountAffected($query);
	}

	public static function dropPolicy($userID, $policyID)
	{
		$query = \DB::prepareQuery("DELETE FROM UserPolicies WHERE userID = %u AND policyID = %u", $userID, $policyID);
		return \DB::queryAndCountAffected($query);
	}

	public static function getIPPools($userID)
	{
		$query = \DB::prepareQuery("SELECT policy, spanStart, (spanStart + spanAppend) AS spanEnd FROM UserSecurityIPs WHERE userID = %u", $userID);
		$result = \DB::queryAndFetchResult($query);

		$pools = array('allow' => new \IPPool(), 'deny' => new \IPPool());

		while($range = \DB::assoc($result))
		{
			if( isset($pools[$range['policy']]) ) $pools[$range['policy']]->addRange(long2ip($range['spanStart']), long2ip($range['spanEnd']));
		}

		return $pools;
	}

	public static function getPasswordCrypto($userID)
	{
		$query = \DB::prepareQuery("SELECT passwordCrypto FROM Users WHERE ID = %u", $userID);
		$passwordCrypto = \DB::queryAndFetchOne($query);
		return $passwordCrypto;
	}

	public static function getPolicies($userID)
	{
		$query = \DB::prepareQuery("SELECT
				p.policy,
				p.ID
			FROM
				Policies p
				JOIN UserPolicies up ON up.policyID = p.ID
			WHERE
				up.userID = %u
			UNION SELECT
				p.policy,
				p.ID
			FROM
				Policies p
				JOIN UserGroupPolicies ugp ON ugp.policyID = p.ID
				JOIN UserGroups ug ON ug.ID = ugp.userGroupID
				JOIN UserGroupUsers ugu ON ugu.userGroupID = ug.ID
			WHERE
				ugu.userID = %u
			ORDER BY
				policy ASC",
			$userID, $userID);

		$policies = \DB::queryAndFetchArray($query);

		return $policies;
	}

	public static function getPreferences($userID)
	{
		$query = \DB::prepareQuery("SELECT preferences FROM Users WHERE ID = %u", $userID);

		$serializedPrefs = \DB::queryAndFetchOne($query);

		$preferences = unserialize($serializedPrefs);

		return is_array($preferences) ? $preferences : array();
	}

	public static function getSettings($userID)
	{
		$query = \DB::prepareQuery("SELECT name, value FROM UserSettings WHERE userID = %u", $userID);
		$settings = \DB::queryAndFetchArray($query);
		return $settings;
	}

	public static function resetPreferences($userID)
	{
		$query = \DB::prepareQuery("UPDATE Users SET preferences = NULL WHERE ID = %u", $userID);
		return \DB::queryAndCountAffected($query);
	}

	public static function resetSettings($userID)
	{
		$query = \DB::prepareQuery("DELETE FROM UserSettings WHERE userID = %u", $userID);
		return \DB::queryAndCountAffected($query);
	}

	public static function setPassword($userID, $password)
	{
		$crypto = self::getPasswordCrypto($userID);

		if( strlen($crypto) != 128 )
		{
			trigger_error("Invalid crypto for userID \"$userID\" in database", E_USER_WARNING);
			return false;
		}

		$passwordHash = \User::createHash($password, $crypto);

		sleep(1); ### Delay to always make sure we get fresh timePasswordLastChange (a little awkward but we're not doing batch runs here)

		$query = \DB::prepareQuery("UPDATE
				Users
			SET
				passwordHash = %s,
				timePasswordLastChange = UNIX_TIMESTAMP()
			WHERE
				ID = %u",
			$passwordHash,
			$userID);

		#throw New \Exception($query);

		$res = \DB::queryAndCountAffected($query);

		return (bool)$res;
	}

	public static function setPasswordCrypto($userID, $crypto)
	{
		$query = \DB::prepareQuery("UPDATE Users SET passwordCrypto = %s WHERE ID = %u", $crypto, $userID);
		return (bool)\DB::queryAndCountAffected($query);
	}

	public static function setPreferences($userID, $preferences)
	{
		if( is_array($preferences) )
		{
			$query = \DB::prepareQuery("UPDATE Users SET preferences = %s WHERE ID = %u", serialize($preferences), $userID);
			return \DB::queryAndCountAffected($query);
		}
		return false;
	}

	public static function setSettings($userID, array $settings)
	{
		self::resetSettings($userID);

		if( count($settings) > 0 )
		{
			$query = "INSERT INTO UserSettings (userID, name, value) VALUES";
			foreach($settings as $key => $value)
			{
				$query .= \DB::prepareQuery('(%u, %s, %s),', $userID, $key, $value);
			}
			\DB::queryAndCountAffected(rtrim($query, ','));
		}

		return true;
	}
}
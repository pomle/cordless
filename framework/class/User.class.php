<?
class User
{
	const TOKEN_COOKIE_LIFE = 172800;
	const TOKEN_LIFE = 172800;

	const USERNAME_MIN_LEN = 1;
	const USERNAME_MAX_LEN = 32;

	const PASSWORD_SALT = 'e7cb508cf04f1da9fd8965978d347133';
	const PASSWORD_MIN_LEN = 6;
	const PASSWORD_MAX_AGE = null;

	const FAIL_LOCK = 10;

	private
		$ip,
		$userID,
		$username,
		$isEnabled,
		$isAdministrator,
		$isLoggedIn,
		$policies,
		$IPsAllowed,
		$IPsDenied;

	protected
		$timeKickOut,
		$timeLastActivity;

	public
		$preferences,
		$settings;


	public static function createCrypto()
	{
		return self::createHash(uniqid(true), self::PASSWORD_SALT);
	}

	public static function createHash($password, $crypto)
	{
		$hash = $password;

		$counter = 0;
		do
		{
			$hash = hash('sha512', $hash . $crypto);
			$counter++;
		}
		while($counter < 10);

		return $hash;
	}

	public static function loadFromDB($userIDs)
	{
		$users = array();

		$query = \DB::prepareQuery("SELECT
				u.ID AS userID,
				u.isEnabled,
				u.isAdministrator,
				u.timeAutoLogout,
				u.timeCreated,
				u.timeModified,
				u.timeLastLogin,
				u.timePasswordLastChange,
				u.countLoginsSuccessful,
				u.countLoginsFailed,
				u.countLoginsFailedStreak,
				u.username,
				u.fullname,
				u.phone,
				u.email
			FROM
				Users u
			WHERE
				u.ID IN %a", $userIDs);

		$result = \DB::fetch($query);

		while($user = \DB::assoc($result))
		{
			$userID = (int)$user['userID'];

			$User = new \User($userID);

			$User->isEnabled = (bool)$user['isEnabled'];
			$User->isAdministrator = (bool)$user['isAdministrator'];
			$User->username = $user['username'];
			$User->timeAutoLogout = (int)$user['timeAutoLogout'] ?: null;
			$User->timeCreated = (int)$user['timeCreated'] ?: null;
			$User->timeModified = (int)$user['timeModified'] ?: null;
			$User->timeLastLogin = (int)$user['timeLastLogin'] ?: null;
			$User->timePasswordLastChange = (int)$user['timePasswordLastChange'] ?: null;

			$User->countLoginsSuccessful = (int)$user['countLoginsSuccessful'];
			$User->countLoginsFailed = (int)$user['countLoginsFailed'];
			$User->countLoginsFailedStreak = (int)$user['countLoginsFailedStreak'];

			$User->fullname = $user['fullname'];
			$User->name = $User->fullname ?: $User->username;
			$User->email = $user['email'];
			$User->phone = $user['phone'];

			$users[$userID] = $User;
		}

		return $users;
	}

	public static function loadOneFromDB($userID)
	{
		return reset(self::loadFromDB(array($userID)));
	}

	public static function login($username, $password = null, $trialToken = null)
	{
		if( !strlen($username) || !strlen($password) && !strlen($trialToken) ) return false;

		$query = \DB::prepareQuery("SELECT
				ID AS userID,
				passwordHash,
				passwordCrypto,
				passwordAuthtoken,
				timeAuthtokenCreated
			FROM
				Users
			WHERE
				isEnabled = 1
				AND username = %s LIMIT 1",
			$username);

		### No DB Entry Found
		if( !$user = \DB::queryAndFetchOne($query) ) return false;

		list($userID, $storedHash, $crypto, $storedToken, $timeToken) = array_values($user);

		if( isset($password) && strlen($password) )
		{
			$trialHash = self::createHash($password, $crypto);

			if( $trialHash !== $storedHash )
			{
				$query = \DB::prepareQuery("UPDATE
						Users
					SET
						countLoginsFailed = countLoginsFailed + 1,
						countLoginsFailedStreak = countLoginsFailedStreak + 1,
						isEnabled = (countLoginsFailedStreak < %u),
						passwordAuthtoken = NULL,
						timeAuthtokenCreated = NULL
					WHERE
						ID = %u",
					self::FAIL_LOCK,
					$userID);

				\DB::queryAndCountAffected($query);

				sleep(2);

				return false;
			}
		}
		elseif( isset($trialToken) && strlen($trialToken) )
		{
			if( strlen($storedToken) !== 128 )
				return false; ### Saved token invalid

			if( ($timeToken + self::TOKEN_LIFE) < time() )
				return false; ### Token has expired

			if( $storedToken !== $trialToken )
				return false; ### Token in DB mismatches token in Cookie
		}
		else
		{
			return false;
		}

		if( !$User = reset(self::loadFromDB(array($userID))) )
			return false;

		$User->isLoggedIn = true;

		$User->enforceSecurity();

		$User->settings = \Manager\User::getSettings($User->userID);
		$User->preferences = \Manager\User::getPreferences($User->userID);

		$newToken = self::createHash(md5($User->username . microtime()), self::PASSWORD_SALT);

		setcookie('username', $User->username, time() + 60*60*24*30, '/');
		setcookie('authtoken',	$newToken, time() + self::TOKEN_COOKIE_LIFE, '/');

		$query = \DB::prepareQuery("UPDATE
				Users
			SET
				countLoginsSuccessful = countLoginsSuccessful + 1,
				countLoginsFailedStreak = 0,
				timeLastLogin = UNIX_TIMESTAMP(),
				passwordAuthtoken = %s,
				timeAuthtokenCreated = UNIX_TIMESTAMP()
			WHERE
				ID = %u",
			$newToken,
			$User->getID());

		\DB::queryAndCountAffected($query);

		return $User;
	}


	public function __construct($userID = 0)
	{
		$this->ip = getenv('REMOTE_ADDR');
		$this->userID = (int)$userID;
		$this->isAdministrator = false;
		$this->isLoggedIn = false;

		$this->IPsAllowed = new \IPPool();
		$this->IPsDenied = new \IPPool();

		$this->settings = array();
		$this->preferences = array();
	}

	public function __destruct()
	{
		if( $this->isLoggedIn() )
		{
			\Manager\User::setPreferences($this->userID, $this->preferences);
			\Manager\User::setSettings($this->userID, $this->settings);
		}
	}

	public function __get($key)
	{
		return $this->$key;
	}

	public function __wakeup()
	{
		if( $this->isLoggedIn() )
			$this->enforceSecurity();
	}


	public function enforceSecurity()
	{
		$this->updateSecurity();

		$kick = false;

		### Is IPs in Allow-pool, make sure we're in it
		if( count($this->IPsAllowed->ranges) )
			$kick = !$this->IPsAllowed->hasIP(CLIENT_HOST_ADDRESS);

		### If IPs in Deny-pool, override allow and kick no matter what
		if( count($this->IPsDenied->ranges) )
			$kick = $this->IPsDenied->hasIP(CLIENT_HOST_ADDRESS);


		### If user has been idle for too long, kick him
		if( $this->timeKickOut && time() >= $this->timeKickOut )
			$kick = true;



		if( $this->isEnabled !== true )
			$kick = true;


		if( $kick )
			$this->isLoggedIn = false;
	}


	public function getID()
	{
		return $this->userID;
	}

	public function getIP()
	{
		return $this->ip;
	}

	public function getPolicies()
	{
		return $this->policies;
	}

	public function getSetting($key)
	{
		if( isset($this->settings[$key]) )
		{
			return $this->settings[$key];
		}
		else
		{
			$defaults = \Manager\Dataset\UserSetting::getAvailable();
			return isset($defaults[$key]) ? $defaults[$key]['default'] : null;
		}
	}

	public function hasPolicy($policy)
	{
		return ( ($this->isAdministrator === true) || isset($this->policies[$policy]) );
	}

	public function hasPolicies($policies)
	{
		$policies = is_array($policies) ? $policies : func_get_args();

		foreach($policies as $policy)
			if( $this->hasPolicy($policy) === false ) return false;

		return true;
	}

	public function hasAnyPolicy($policies)
	{
		$policies = is_array($policies) ? $policies : func_get_args();

		foreach($policies as $policy)
			if( $this->hasPolicy($policy) === true ) return true;

		return false;
	}

	public function isAdministrator()
	{
		return ($this->isAdministrator === true);
	}

	public function isLoggedIn()
	{
		return $this->isLoggedIn;;
	}

	public function logout()
	{
		if( $this->isLoggedIn !== true ) return false;

		$query = \DB::prepareQuery("UPDATE
			Users
				SET
					passwordAuthtoken = NULL,
					timeAuthtokenCreated = NULL
			WHERE
				ID = %u",
			$this->userID);

		\DB::queryAndCountAffected($query);

		$this->isLoggedIn = false;

		setcookie('authtoken', '', 0, '/');

		return true;
	}

	public function setSetting($key, $value = null)
	{
		$key = (string)$key;
		$value = (string)$value;

		if( !strlen($value) && isset($this->settings[$key]) )
		{
			unset($this->settings[$key]);
		}
		elseif( strlen($value) )
		{
			$this->settings[$key] = (string)$value;
		}

		return true;
	}

	protected function updateSecurity()
	{
		$properties = \Manager\Dataset\User::getProperties($this->userID);

		$this->isEnabled = (bool)$properties['isEnabled'];
		$this->isAdministrator = (bool)$properties['isAdministrator'];

		$this->username = $properties['username'];

		$this->timeLastActivity = time();
		$this->timeKickOut = is_numeric($properties['timeAutoLogout']) ? $this->timeLastActivity + $properties['timeAutoLogout'] : null;

		$this->policies = \Manager\User::getPolicies($this->userID);

		$ipPools = \Manager\User::getIPPools($this->userID);

		$this->IPsAllowed = $ipPools['allow'];
		$this->IPsDenied = $ipPools['deny'];
	}
}
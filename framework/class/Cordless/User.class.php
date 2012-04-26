<?
namespace Cordless;

class User extends \Asenine\User
{
	public
		$last_fm_username,
		$last_fm_key,
		$last_fm_scrobble,
		$last_fm_love_starred_tracks,
		$last_fm_unlove_unstarred_tracks;

	public static function loadFromDB($userIDs)
	{
		if( !$returnArray = is_array($userIDs) )
			$userIDs = (array)$userIDs;

		$users = parent::loadFromDB($userIDs);

		$userIDs = array_keys($users);

		$query = \Asenine\DB::prepareQuery("SELECT
				cu.userID,
				cu.last_fm_username,
				cu.last_fm_key,
				cu.last_fm_scrobble,
				cu.last_fm_love_starred_tracks,
				cu.last_fm_unlove_unstarred_tracks
			FROM
				Cordless_Users cu
			WHERE
				cu.userID IN %a",
			$userIDs);

		$Result = \Asenine\DB::queryAndFetchResult($query);

		foreach($Result as $user)
		{
			$userID = (int)$user['userID'];

			if( isset($users[$userID]) )
			{
				$User = $users[$userID];

				$User->last_fm_username = $user['last_fm_username'];
				$User->last_fm_key = $user['last_fm_key'];

				$User->last_fm_scrobble = (bool)$user['last_fm_scrobble'];
				$User->last_fm_love_starred_tracks = (bool)$user['last_fm_love_starred_tracks'];
				$User->last_fm_unlove_unstarred_tracks = (bool)$user['last_fm_unlove_unstarred_tracks'];
			}
		}

		return $returnArray ? $users : reset($users);
	}

	public static function saveToDB(self $User)
	{
		$query = \Asenine\DB::prepareQuery("INSERT INTO
			Cordless_Users
			(
				userID,
				last_fm_username,
				last_fm_key,
				last_fm_scrobble,
				last_fm_love_starred_tracks,
				last_fm_unlove_unstarred_tracks
			) VALUES(
				%d,
				NULLIF(%s, ''),
				NULLIF(%s, ''),
				%d,
				%d,
				%d
			) ON DUPLICATE KEY UPDATE
				last_fm_username = VALUES(last_fm_username),
				last_fm_key = VALUES(last_fm_key),
				last_fm_scrobble = VALUES(last_fm_scrobble),
				last_fm_love_starred_tracks = VALUES(last_fm_love_starred_tracks),
				last_fm_unlove_unstarred_tracks = VALUES(last_fm_unlove_unstarred_tracks)",
			$User->userID,
			$User->last_fm_username,
			$User->last_fm_key,
			$User->last_fm_scrobble,
			$User->last_fm_love_starred_tracks,
			$User->last_fm_unlove_unstarred_tracks);

		\Asenine\DB::query($query);
	}

	public function getFriendUserIDs()
	{
		$query = \Asenine\DB::prepareQuery("SELECT userID FROM Cordless_UserFriends WHERE friendUserID = %d", $this->userID);
		return \Asenine\DB::queryAndFetchArray($query);
	}

	public function getFriendsUserIDs()
	{
		$query = \Asenine\DB::prepareQuery("SELECT friendUserID FROM Cordless_UserFriends WHERE userID = %d", $this->userID);
		return \Asenine\DB::queryAndFetchArray($query);
	}

	public function getSetting($key)
	{
		return parent::getSetting("Cordless." . $key);
	}

	public function isFriend($friendUserID)
	{
		return in_array($friendUserID, $this->getFriendsUserIDs());
	}

	public function isUserIDAccessible($userID)
	{
		if( $userID == $this->userID )
			return true;

		if( in_array($userID, $this->getFriendUserIDs()) )
			return true;

		return false;
	}

	public function setSetting($key, $value = null)
	{
		return parent::setSetting("Cordless." . $key, $value);
	}
}
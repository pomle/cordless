<?
namespace Cordless;

function APIMethod($User, $params)
{
	list($friendUserID) = ensureParams($params, 'friendUserID');


	if( !$Friend = User::loadFromDB($friendUserID) )
		throw new APIException('Friend user does not exist');


	$beFriend = function($userID, $friendUserID)
	{
		$query = \Asenine\DB::prepareQuery("INSERT IGNORE INTO
			Cordless_UserFriends (
				userID,
				friendUserID
			) VALUES(
				%d,
				%d)",
			$userID,
			$friendUserID);

		\Asenine\DB::query($query);
	};

	$unFriend = function($userID, $friendUserID)
	{
		$query = \Asenine\DB::prepareQuery("DELETE FROM
				Cordless_UserFriends
			WHERE
				userID = %d
				AND friendUserID = %d",
			$userID,
			$friendUserID);

		\Asenine\DB::query($query);
	};


	if( isset($params->action) )
	{
		switch($params->action)
		{
			case 'befriend':
				$beFriend($User->userID, $Friend->userID);
			break;

			case 'toggle':
				if( in_array($Friend->userID, $User->getFriendsUserIDs()) )
					$unFriend($User->userID, $Friend->userID);
				else
					$beFriend($User->userID, $Friend->userID);
			break;

			case 'unfriend':
				$unFriend($User->userID, $Friend->userID);
			break;
		}
	}


	return array(
		'friendUserID' => $Friend->userID,
		'username' => $Friend->username,
		'isFriend' => ($isFriend = in_array($Friend->userID, $User->getFriendsUserIDs())),
		'message' => sprintf($isFriend ? _('You are now friends with %s') : _('You have unfriended %s'), $Friend->username)
	);
}
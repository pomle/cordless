<?
namespace Cordless;

function APIMethod($User, $params)
{
	list($friendUserID) = ensureParams($params, 'friendUserID');


	if( !$Friend = User::loadFromDB($friendUserID) )
		throw new APIException('Friend user does not exist');


	if( isset($params->action) )
	{
		switch($params->action)
		{
			case 'befriend':
				$query = \Asenine\DB::prepareQuery("INSERT IGNORE INTO
					Cordless_UserFriends (
						userID,
						friendUserID
					) VALUES(
						%d,
						%d)",
					$User->userID,
					$Friend->userID);

				\Asenine\DB::query($query);
			break;

			case 'unfriend':
				$query = \Asenine\DB::prepareQuery("DELETE FROM
						Cordless_UserFriends
					WHERE
						userID = %d
						AND friendUserID = %d",
					$User->userID,
					$Friend->userID);

				\Asenine\DB::query($query);
			break;
		}
	}


	return array(
		'friendUserID' => $Friend->userID,
		'username' => $Friend->username,
		'isFriend' => in_array($Friend->userID, $User->getFriendsUserIDs())
	);
}
<?
namespace Cordless;

function APIMethod($User, $params)
{
	list($action) = ensureParams($params, 'action');

	switch($params->action)
	{
		case 'lookupUsername':
			list($username) = ensureParams($params, 'username');

			if( !($userID = \Asenine\User\Dataset::getUserID($username)) || !($Subject = User::loadFromDB($userID)) )
				throw new APIException('User does not exist');

			return array
			(
				'userID' => $Subject->userID,
				'username' => $Subject->username
			);
		break;
	}

	throw new APIException('Invalid action');
}
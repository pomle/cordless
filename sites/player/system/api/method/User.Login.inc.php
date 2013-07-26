<?
namespace Cordless;

$requireLogin = false;
$keepSession = true;

function APIMethod($User, $params)
{
	list($username, $password) = ensureParams($params, 'username', 'password');

	if( !$User = User::login($username, $password, null) )
		throw new APIException("Credentials");

	$_SESSION['User'] = $User;

	return array(
		'userID' => $User->userID,
		'username' => $User->username,
		'token' => session_id()
	);
}
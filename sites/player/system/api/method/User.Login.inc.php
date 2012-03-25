<?
namespace Cordless;

$requireLogin = false;
$keepSession = true;

function APIMethod($User, $params)
{
	ensureParams($params, 'username', 'password');

	$username = $params['username'];
	$password = $params['password'];

	if( !$User = User::login($username, $password, null) )
		throw new APIException("Credentials");

	$_SESSION['User'] = $User;

	return array(
		'userID' => $User->userID,
		'username' => $User->username
	);
}
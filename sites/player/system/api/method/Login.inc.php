<?
namespace Cordless;

$requireLogin = false;
$keepSession = true;

function APIMethod($User, $params)
{
	if( !isset($params['username']) )
		throw New APIException("Username missing");

	if( !isset($params['password']) )
		throw New APIException("Password missing");

	$username = $params['username'];
	$password = $params['password'];

	if( !$User = User::login($username, $password, null) )
		throw New APIException("Credentials");

	$User->session_key = 'POMLEAUTH';

	$_SESSION['User'] = $User;

	return array('username' => $User->username, 'sessionKey' => $User->session_key);
}
<?
namespace Cordless;

if( !isset($_SESSION['User']) || !$_SESSION['User'] instanceof User || isset($_POST['login']) )
{
	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : null;
	$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : null;
	$authtoken = isset($_COOKIE['authtoken']) ? $_COOKIE['authtoken'] : null;

	$_SESSION['User'] = User::login($username, $password, $authtoken) ?: new User();
}
$User = $_SESSION['User'];

define('USER_ID', $User->userID);
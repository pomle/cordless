<?
require DIR_ADMIN_INCLUDE . 'Functions.User.inc.php';

if( !isset($_SESSION) ) trigger_error('Session not started', E_USER_ERROR);

if( !isset($_SESSION['User']) || !$_SESSION['User'] instanceof \Asenine\User || isset($_POST['login']) )
{


	if( isset($_POST['username']) )
		$username = $_POST['username'];
	elseif( isset($_COOKIE['username']) )
		$username = $_COOKIE['username'];
	else
		$username = null;

	$password = isset($_POST['password']) ? $_POST['password'] : null;
	$authtoken = isset($_COOKIE['authtoken']) ? $_COOKIE['authtoken'] : null;

	$_SESSION['User'] = \Asenine\User::login($username, $password, $authtoken) ?: new \Asenine\User();
}
$User = $_SESSION['User'];

if( USER_IP_SECURITY === true && $User->getIP() !== getenv('REMOTE_ADDR') )
{
	trigger_error('IP Changed. Possible cookie theft. User session killed', E_USER_WARNING);
	$User->logout();

	$User = new \Asenine\User();
}

if( $User->isLoggedIn() !== true ) require DIR_ADMIN_INIT . 'UserSecurity.init.php';

### For backwards compatibility
$user = $User;

define('USER_ID', $User->getID());
define('USER_IS_ADMIN', $User->isAdministrator());
define('CURRENT_USER_ID', USER_ID); ### Backwardscompatibility

define('LOCALE_ID', $User->getSetting('DefaultLocaleID'));
define('LANGUAGE_LOCALE_ID', LOCALE_ID);
define('CURRENCY_LOCALE_ID', $User->getSetting('DefaultCurrencyLocaleID'));

define('FORMAT_DATETIME', $User->getSetting('DefaultTimeFormat'));
define('FORMAT_TIME', $User->getSetting('DefaultTimeFormat'));
define('FORMAT_DATE', $User->getSetting('DefaultDateFormat'));
define('FORMAT_MONEY_', $User->getSetting('DefaultMoneyFormat'));
define('FORMAT_MONEY_SHORT', $User->getSetting('DefaultMoneyFormatShort'));
define('FORMAT_MONEY_INTERNATIONAL', '%i');


if( defined('ACCESS_POLICY') )
{
	require DIR_ADMIN_INIT . 'AccessSecurity.init.php';
	$userPanel['accessPolicy'] = \Element\Icon::custom('lock', ACCESS_POLICY);
}


$userPanel = array_merge($userPanel, array(
	'profile'	=> sprintf('<a href="%s">%s</a>', '/UserProfile.php', \Element\Icon::custom(USER_IS_ADMIN ? 'user_suit' : 'user', $User->username)),
	'logout'	=> sprintf('<a href="%s">%s</a>', '/Logout.php', \Element\Icon::custom('door_in', _('Logga ut')))
));
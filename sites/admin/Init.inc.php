<?
require __DIR__ . '/Settings.inc.php';
require __DIR__ . '/../Init.inc.php';

foreach
(
	array
	(
		'FORCE_SSL' => true,
		'USER_IP_SECURITY' => true,
	)
	as $name => $value)
{
	if( !defined($name) )
		define($name, $value);
}

if( !defined('DIR_ADMIN') )
	define('DIR_ADMIN', DIR_SITES . 'admin/');

define('DIR_ADMIN_CONFIG', DIR_ADMIN . 'config/');
define('DIR_ADMIN_PUBLIC', DIR_ADMIN . 'public/');
define('DIR_ADMIN_SYSTEM', DIR_ADMIN . 'system/');

define('DIR_ADMIN_CLASS',	DIR_ADMIN_SYSTEM . 'class/');
define('DIR_ADMIN_INCLUDE',	DIR_ADMIN_SYSTEM . 'include/');
define('DIR_ADMIN_INIT',	DIR_ADMIN_SYSTEM . 'init/');
define('DIR_ADMIN_ELEMENT', DIR_ADMIN_SYSTEM . 'element/');
	define('DIR_ADMIN_COMMON',		DIR_ADMIN_INCLUDE . 'common/');
	define('DIR_AJAX_IO',			DIR_ADMIN_INCLUDE . 'io/');

define('URL_FALLBACK_THUMB', '/layout/fallback_thumb.png');
define('URL_FALLBACK_THUMB_ICON', '/layout/fallback_thumb_icon.png');
define('URL_IO_FETCHER', '/ajax/AjaxRequest.php');

define('HEADER', DIR_ADMIN_ELEMENT . 'Header.Improved.inc.php');
define('FOOTER', DIR_ADMIN_ELEMENT . 'Footer.Improved.inc.php');

addIncludePath(DIR_ADMIN_CLASS);

if( !defined('FORCE_SSL') )
	define('FORCE_SSL', true);

if( FORCE_SSL && !HTTPS )
{
	header('Location: https://' . HOST . $_SERVER['REQUEST_URI']);
	die('HTTPS Required');
}

#require DIR_SYSTEM_INIT . 'Locale.init.php';

require DIR_ADMIN_INCLUDE . 'Functions.inc.php';
require DIR_ADMIN_INCLUDE . 'Messages.General.inc.php';

$pageTitlePrefix = 'AsenineAdmin';

$css[] = '/css/Admin.css';
$css[] = '/css/Awesome.css';

$js[] = DEBUG ? '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js' : '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js';

$js[] = '/js/objects/AjaxEvent.js';
$js[] = '/js/objects/FormManager.js';
$js[] = '/js/objects/Messenger.js';

$js[] = '/js/Admin.js';

$userPanel = array();

if( !defined('NO_LOGIN_REQUIRED') )
{
	session_start();
	require DIR_ADMIN_INIT . 'User.init.php';
}
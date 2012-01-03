<?
require __DIR__ . '/Settings.inc.php';

if( !defined('DIR_MEDIA') )
	die('DIR_MEDIA not defined');


if( !defined('DEBUG') )
	define('DEBUG', false);

define('HTTPS', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
define('PROTOCOL', HTTPS ? 'https' : 'http');

define('HOST', $_SERVER['HTTP_HOST']);

$_domain = array_reverse(explode('.', HOST));
$_subdomain = array_slice($_domain, 2);

define('DOMAIN_ACTUAL', $_domain[1] . '.' . $_domain[0]);
define('SUBDOMAIN_ACTUAL', count($_subdomain) ? join('.', $_subdomain) . '.' : '');
define('DOMAIN_FULL', SUBDOMAIN_ACTUAL . DOMAIN_ACTUAL);
define('DOMAIN', DOMAIN_ACTUAL);

unset($_domain, $_subdomain);

define('CLIENT_HOST_ADDRESS', getenv('REMOTE_ADDR'));

if( !defined('DIR_TEMP') )
	define('DIR_TEMP', '/tmp/');

if( !defined('DIR_ASENINE') )
	define('DIR_ASENINE', DIR_ROOT . 'framework/');

if( !defined('DIR_SITES') )
	define('DIR_SITES', DIR_ROOT . 'sites/');

if( !defined('DIR_MEDIA') )
	define('DIR_MEDIA', DIR_ROOT . 'media/');

if( !defined('DIR_MEDIA_SOURCE') )
	define('DIR_MEDIA_SOURCE', DIR_MEDIA . 'source/');

define('DIR_ASENINE_CLASS',		DIR_ASENINE . 'class/');
define('DIR_ASENINE_COMMON',	DIR_ASENINE . 'common/');
define('DIR_ASENINE_CONFIG',	DIR_ASENINE . 'config/');
define('DIR_ASENINE_INCLUDE',	DIR_ASENINE . 'include/');
define('DIR_ASENINE_SITES',		DIR_ASENINE . 'sites/');


define('MEDIA_TYPE_AUDIO',	'audio');
define('MEDIA_TYPE_IMAGE',	'image');
define('MEDIA_TYPE_ROTATE',	'rotate');
define('MEDIA_TYPE_VIDEO',	'video');

if( !defined('CACHE_FORCE_REGENERATE') )
	define('CACHE_FORCE_REGENERATE', isset($_GET['cacheForceRegenerate']));

mb_internal_encoding('UTF-8');

require DIR_ASENINE_INCLUDE . 'Functions.inc.php';

addIncludePath(DIR_ASENINE_CLASS);
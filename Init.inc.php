<?
namespace Asenine;

define('RENDERSTART', microtime(true));

require __DIR__ . '/Settings.Asenine.inc.php';

define('ASENINE_DIR_CLASS',		DIR_ASENINE . 'class/');
define('ASENINE_DIR_COMMON',	DIR_ASENINE . 'common/');
define('ASENINE_DIR_CONFIG',	DIR_ASENINE . 'config/');
define('ASENINE_DIR_INCLUDE',	DIR_ASENINE . 'include/');
define('ASENINE_DIR_SITES',		ASENINE_DIR_ROOT . 'sites/');

require ASENINE_DIR_INCLUDE . 'Functions.Global.inc.php';
require ASENINE_DIR_INCLUDE . 'Functions.Asenine.inc.php';

asenineDef('DEBUG', false);
asenineDef('HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null);

asenineDef('ASENINE_DIR_TEMP', '/tmp/');

asenineDef('ASENINE_DIR_SITES', ASENINE_DIR_ROOT . 'sites/');

asenineDef('DIR_MEDIA', ASENINE_DIR_ROOT . 'media/');
asenineDef('DIR_MEDIA_SOURCE', DIR_MEDIA . 'source/');


define('HTTPS', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
define('PROTOCOL', HTTPS ? 'https' : 'http');

define('ASENINE_MEDIA_TYPE_AUDIO',	'audio');
define('ASENINE_MEDIA_TYPE_IMAGE',	'image');
define('ASENINE_MEDIA_TYPE_ROTATE',	'rotate');
define('ASENINE_MEDIA_TYPE_VIDEO',	'video');

define('ARCHIVE_NAMESPACE_MEDIA_SOURCE', 'media/source');
define('ARCHIVE_NAMESPACE_MEDIA_AUTOGEN', 'media/autogen');

if( !defined('CACHE_FORCE_REGENERATE') )
	define('CACHE_FORCE_REGENERATE', isset($_GET['cacheForceRegenerate']));

mb_internal_encoding('UTF-8');

\addIncludePath(ASENINE_DIR_CLASS);

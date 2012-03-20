<?
namespace Cordless;

require __DIR__ . '/../Init.inc.php';

define('DIR_SITE', ASENINE_DIR_SITES . 'player/');

require DIR_SITE . 'Settings.inc.php';

define('DIR_SITE_SYSTEM', DIR_SITE . 'system/');
define('DIR_SITE_CLASS', DIR_SITE_SYSTEM . 'class/');
define('DIR_SITE_INCLUDE', DIR_SITE_SYSTEM . 'include/');
define('DIR_SITE_RESOURCE', DIR_SITE_SYSTEM . 'resource/');

define('DIR_CORDLESS_API_METHODS', DIR_SITE_SYSTEM . 'api/method/');

define('DIR_ELEMENT', DIR_SITE_SYSTEM . 'element/');
define('DIR_ELEMENT_PANEL', DIR_ELEMENT . 'panel/');

require DIR_SITE_INCLUDE . 'Functions.inc.php';
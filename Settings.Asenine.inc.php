<?
namespace Asenine;

### Asenine Settings Config
define('DEBUG', true); ### When DEBUG is true, sensitive information like like file paths and database queries might be shown. Make sure it is "false" on production
define('FORCE_SSL', false); ### Never enforce SSL

### PHP Settings Config
error_reporting(DEBUG ? E_ALL : 0);
date_default_timezone_set('Europe/Berlin');

### Dir Config
define('ASENINE_DIR_ROOT', __DIR__ . '/');

define('DIR_ASENINE', ASENINE_DIR_ROOT . 'framework/'); ### This constant can be guessed by system but it is recommended to point this to "framework" dir

### Make this entire tree writeable by the PHP user
define('DIR_MEDIA', ASENINE_DIR_ROOT . 'media/');
define('DIR_MEDIA_PUBLIC', DIR_MEDIA . 'public/');
define('DIR_MEDIA_SOURCE', DIR_MEDIA . 'source/');

#define('DIR_LOG', ASENINE_DIR_ROOT . 'log/'); ### If you want to enable logging, uncomment this line and make this dir writeable

### Asenine Database Config
define('ASENINE_PDO_DSN', 'mysql:host=localhost;dbname=Asenine');
define('ASENINE_PDO_USER', 'asenine');
define('ASENINE_PDO_PASS', 'asenine');


define('ASENINE_USER_PASSWORD_SALT', '09714n1972n510957n10f101n'); ### Set this to something funny
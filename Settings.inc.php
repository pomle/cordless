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

define('ASENINE_DIR', ASENINE_DIR_ROOT . 'framework/'); ### This constant can be guessed by system but it is recommended to point this to "framework" dir

### You can customize where uploads and generated files are put. If you don't specify, everything will be put in archive/ subfolder of project dir
#define('ASENINE_DIR_ARCHIVE', ASENINE_DIR_ROOT . 'archive/');
#define('ASENINE_DIR_MEDIA', ASENINE_DIR_ARCHIVE . 'media/');
#define('ASENINE_DIR_MEDIA_PUBLIC', ASENINE_DIR_MEDIA . 'public/');


### If you want to enable logging, uncomment this line and make dir writeable
#define('ASENINE_DIR_LOG', ASENINE_DIR_ROOT . 'log/');

### Asenine Database Config
define('ASENINE_PDO_DSN', 'mysql:host=localhost;dbname=Asenine');
define('ASENINE_PDO_USER', 'asenine');
define('ASENINE_PDO_PASS', 'asenine');


define('ASENINE_USER_PASSWORD_SALT', ''); ### Set this to something funny
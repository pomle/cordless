<? 
/*
	FLIPSHOP INSTALL SCRIPT
*/

$settingsTemplate = "<?
// Set Error Reporting
error_reporting(E_ALL - E_NOTICE);

define('DEBUG', %DEBUG%); // Are we debugging?
define('DEBUG_EMAIL', '%DEBUG_EMAIL%');

define('PARSETIME', microtime(true)); // For Page Generation Timing

define('CACHE_PREFIX', '%CACHE_PREFIX%'); // Set Memcache Cache Prefix


define('HOST', \$_SERVER['SERVER_NAME']); // Constant Host

// Database Config
define('DB_HOST',	'%DB_HOST%');
define('DB_USER',	'%DB_USER%');
define('DB_PASS',	'%DB_PASS%');
define('DB_NAME',	'%DB_NAME%');
define('DB_CHARSET','utf8');


// Mail Config
define('SMTP_HOST', '%SMTP_HOST%');
define('SMTP_PORT', %SMTP_PORT%);

define('SYSTEM_USER_NAME',	'%SYSTEM_USER_NAME%');
define('SYSTEM_USER_MAIL_NAME',	'%SYSTEM_USER_MAIL_NAME%');
define('SYSTEM_USER_MAIL_ADDRESS',	'%SYSTEM_USER_MAIL_ADDRESS%');

// Formatting
define('SYSTEM_TIMESTAMP_FORMAT', '%SYSTEM_TIMESTAMP_FORMAT%');
define('SYSTEM_TIME_FORMAT', '%SYSTEM_TIME_FORMAT%');
define('SYSTEM_DATE_FORMAT', '%SYSTEM_DATE_FORMAT%');

// Dir Config
define('ASENINE_DIR_ROOT', '%ASENINE_DIR_ROOT%'); // Installation Dir
	
	define('ASENINE_DIR_LOG', ASENINE_DIR_ROOT.'log/');
	define('ASENINE_DIR_TEMP', '/tmp/');

	define('DIR_SYSTEM',			ASENINE_DIR_ROOT.'system/');
		define('DIR_SYSTEM_CONFIG',	DIR_SYSTEM.'config/');
		define('DIR_SYSTEM_CLASS',	DIR_SYSTEM.'class/');
		define('DIR_SYSTEM_MODULE',	DIR_SYSTEM.'module/');
		define('DIR_SYSTEM_COMMON',	DIR_SYSTEM.'common/');
			define('DIR_SYSTEM_MODULE_CHECKOUT',	DIR_SYSTEM_MODULE.'checkout/');

		define('DIR_SYSTEM_INCLUDE',	DIR_SYSTEM.'include/');

	define('DIR_LANGUAGE', ASENINE_DIR_ROOT.'language/');

	define('DIR_STORE', ASENINE_DIR_ROOT.'sites/store/');
		define('DIR_STORE_SYSTEM', DIR_STORE.'system/');
		define('DIR_STORE_PUBLIC', DIR_STORE.'public/');

	define('DIR_ADMIN', ASENINE_DIR_ROOT.'sites/admin/');
		define('DIR_ADMIN_SYSTEM', DIR_ADMIN.'system/');
		define('DIR_ADMIN_PUBLIC', DIR_ADMIN.'public/');


	define('DIR_MEDIA', ASENINE_DIR_ROOT.'media/');
		define('DIR_MEDIA_SOURCE',	DIR_MEDIA.'source/');
		
		define('DIR_IMAGE',			DIR_MEDIA.'image/');
		define('DIR_VIDEO',			DIR_MEDIA.'video/');
		define('DIR_ROTATE',		DIR_MEDIA.'rotate/');
		define('DIR_GRAPHICS',		DIR_MEDIA.'graphics/');

		define('DIR_MEDIA_BULLETIN', DIR_GRAPHICS.'bulletin/');
	
	define('ASENINE_DIR_ARCHIVE', ASENINE_DIR_ROOT.'archive/');
		define('DIR_PRESS',		ASENINE_DIR_ARCHIVE.'press/');
		define('DIR_PUBLIC',	ASENINE_DIR_ARCHIVE.'public/');
		define('DIR_WORKORDER',	ASENINE_DIR_ARCHIVE.'workorder/');


	define('DIR_RESOURCE', DIR_SYSTEM.'resources/');
		define('DIR_RESOURCE_FONT', DIR_RESOURCE.'font/');
		define('DIR_RESOURCE_GRAPHICS', DIR_RESOURCE.'graphics/');

define('USERGROUP_ADMINS', NULL);
define('USERGROUP_CUSTOMER_SERVICE', NULL);

		

// Executables
define('EXECUTABLE_IDENTIFY',	'%EXECUTABLE_IDENTIFY%');
define('EXECUTABLE_CONVERT',	'%EXECUTABLE_CONVERT%');
define('EXECUTABLE_MOGRIFY',	'%EXECUTABLE_MOGRIFY%');
define('EXECUTABLE_FFMPEG',		'%EXECUTABLE_FFMPEG%');


// Country Config
define('SWEDEN', 1);
define('DENMARK', 2);
define('FINLAND', 3);
define('GERMANY', 4);


// Kreditor Config
define('KREDITOR_EID', 436);
define('KREDITOR_SECRET', 'ZjZoOlzCCZBEk6F');

define('KREDITOR_HOST', 'payment.kreditor.se');
define('KREDITOR_PORT', 80);";

define('ASENINE_DIR_ROOT', __DIR__.'/../../../');
$settingsFile = ASENINE_DIR_ROOT.'system/Settings.inc.php';

if( file_exists($settingsFile) ) {
	die('Already configured...');
}

if( !is_writeable(ASENINE_DIR_ROOT.'system/') ) {
	die('Settings path not writeable...');
}

if( isset($_POST['install']) ) {

	$settingsContent = str_replace(
		array(
			'%DEBUG%',
			'%DEBUG_EMAIL%',

			'%CACHE_PREFIX%',

			'%DB_HOST%',
			'%DB_USER%',
			'%DB_PASS%',
			'%DB_NAME%',
			'%SMTP_HOST%',
			'%SMTP_PORT%',

			'%SYSTEM_USER_NAME%',
			'%SYSTEM_USER_MAIL_NAME%',
			'%SYSTEM_USER_MAIL_ADDRESS%',

			'%SYSTEM_TIMESTAMP_FORMAT%',
			'%SYSTEM_TIME_FORMAT%',
			'%SYSTEM_DATE_FORMAT%',

			'%ASENINE_DIR_ROOT%'
		),
		array(
			(int)$_POST['DEBUG'],
			$_POST['DEBUG_EMAIL'],

			$_POST['CACHE_PREFIX'],

			$_POST['DB_HOST'],
			$_POST['DB_USER'],
			$_POST['DB_PASS'],
			$_POST['DB_NAME'],
			$_POST['SMTP_HOST'],
			(int)$_POST['SMTP_PORT'],

			$_POST['SYSTEM_USER_NAME'],
			$_POST['SYSTEM_USER_MAIL_NAME'],
			$_POST['SYSTEM_USER_MAIL_ADDRESS'],

			$_POST['SYSTEM_TIMESTAMP_FORMAT'],
			$_POST['SYSTEM_TIME_FORMAT'],
			$_POST['SYSTEM_DATE_FORMAT'],

			$_POST['ASENINE_DIR_ROOT']
		),
		$settingsTemplate
	);

	header('Content-type: text/plain');

	file_put_contents($settingsFile, $settingsContent);

	header('Location: /');
}
?>
<html>
	<head>
		<style type="text/css">
			input[type=text] {
				width: 30em;
			}
		</style>
	</head>
	<body>
		<form action="" method="POST">
			<table>
			<tr><td>DEBUG</td><td><input type="radio" name="DEBUG" value="1">1 <input type="radio" name="DEBUG" value="0">0 </td></tr>

			<tr><td>ASENINE_DIR_ROOT</td><td><input type="text" name="ASENINE_DIR_ROOT" value="<? echo htmlspecialchars(str_replace('sites/admin/public', '', __DIR__)); ?>" /></td></tr>

			<tr><td>DB_HOST</td><td><input type="text" name="CACHE_PREFIX" value="FLIPSHOP" /></td></tr>

			<tr><td>DB_HOST</td><td><input type="text" name="DB_HOST" value="localhost" /></td></tr>
			<tr><td>DB_USER</td><td><input type="text" name="DB_USER" value="" /></td></tr>
			<tr><td>DB_PASS</td><td><input type="text" name="DB_PASS" value="" /></td></tr>
			<tr><td>DB_NAME</td><td><input type="text" name="DB_NAME" value="flipshop" /></td></tr>
			<tr><td>SMTP_HOST</td><td><input type="text" name="SMTP_HOST" value="localhost" /></td></tr>
			<tr><td>SMTP_PORT</td><td><input type="text" name="SMTP_PORT" value="25" /></td></tr>

			<tr><td>SYSTEM_USER_NAME</td><td><input type="text" name="SYSTEM_USER_NAME" value="Admin" /></td></tr>
			<tr><td>SYSTEM_USER_MAIL_NAME</td><td><input type="text" name="SYSTEM_USER_MAIL_NAME" value="Admin" /></td></tr>
			<tr><td>SYSTEM_USER_MAIL_ADDRESS</td><td><input type="text" name="SYSTEM_USER_MAIL_ADDRESS" value="admin@" /></td></tr>

			<tr><td>SYSTEM_TIMESTAMP_FORMAT</td><td><input type="text" name="SYSTEM_TIMESTAMP_FORMAT" value="%Y-%m-%d %H:%M" /></td></tr>
			<tr><td>SYSTEM_TIME_FORMAT</td><td><input type="text" name="SYSTEM_TIME_FORMAT" value="%H:%M:%S" /></td></tr>
			<tr><td>SYSTEM_DATE_FORMAT</td><td><input type="text" name="SYSTEM_DATE_FORMAT" value="%Y-%m-%d" /></td></tr>

			<tr><td colspan="2"><input type="submit" name="install" value="Install" /></td></tr>

			</table>
		</form>
	</body>
</html>
<?



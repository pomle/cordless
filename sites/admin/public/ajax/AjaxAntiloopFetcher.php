<?
define('IS_AJAX_REQUEST', true);

### Simulates a server failure
#header('HTTP/1.0 500 - Internal Server Error'); die();

require '../../Init.inc.php';

try
{
	$params = array_merge($_GET, $_POST);

	ob_start();
	echo \Element\Antiloop::getAsDomObject($_GET['protocol'], $params)->getInnerHTML();
	ob_end_flush();
}
catch(Exception $e)
{
	ob_end_clean();
	$action = 'error';
	Message::flushMessages('notice');
	Message::addError($e->getMessage());
}
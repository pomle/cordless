<?
namespace Cordless;

require '../../Init.Application.inc.php';
require DIR_SITE_INCLUDE . 'Functions.API.inc.php';

class APIException extends \Exception
{}

class ParamException extends \Exception
{}

header("Content-Type: application/json");

try
{
	$requireLogin = true;
	$keepSession = false;

	if( !isset($_GET['method']) || preg_match('/[^A-Za-z\.]/', $_GET['method']) )
		throw New APIException("Method Invalid");

	$method = $_GET['method'];


	if( !file_exists($methodFile = DIR_CORDLESS_API_METHODS . $method . '.inc.php') )
		throw DEBUG ? new \Exception("Method file missing: " . $methodFile) : new APIException("Method " . $method . " does not exist");

	require $methodFile;


	session_start();
	require DIR_SITE_SYSTEM . 'init/User.inc.php';

	if( $keepSession !== true )
		session_write_close();

	if( $requireLogin !== false )
		ensureLogin($User);


	jsonResponse(true, APIMethod($User, $_POST ? $_POST : $_GET));
}
catch(ParamException $e)
{
	jsonResponse(false, sprintf('Missing parameter "%s"', $e->getMessage()));
}
catch(APIException $e)
{
	jsonResponse(false, $e->getMessage());
}
catch(\Exception $e)
{
	header("HTTP/1.1 500 Internal Server Error");
	jsonResponse(false, DEBUG ? $e->getMessage() : ERROR_APPLICATION);
}
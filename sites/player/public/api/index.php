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


	### POST has priority over GET. If parameter name "params" are set in any response we excpect it to be JSON. JSON is always preferred since it obeys strict handling of data types as NULL, true/false and integers
	if( isset($_POST) && count($_POST) )
	{
		if( isset($_POST['params']) )
		{
			if( !$params = json_decode($_POST['params']) )
				throw new APIException('POST "params" is not valid JSON');
		}
		else
			$params = (object)$_POST;
	}
	else
	{
		if( isset($_GET['params']) )
		{
			if( !$params = json_decode($_GET['params']) )
				throw new APIException('GET "params" is not valid JSON');
		}
		else
			$params = (object)$_GET;
	}

	if( !$params instanceof \stdClass )
		throw new \Exception("Parameters could not be cast as object");

	jsonResponse(true, APIMethod($User, $params));
}
catch(ParamException $e)
{
	jsonResponse(false, sprintf('Missing required parameter "%s"', $e->getMessage()));
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
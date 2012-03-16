<?
namespace Cordless;

require '../../Init.Application.inc.php';
require DIR_SITE_INCLUDE . 'Functions.API.inc.php';

class APIException extends \Exception
{}

class ParamException extends \Exception
{}

define("IS_USING_TOKEN", false); //!(bool)preg_match('/Mozilla/', $_SERVER['HTTP_USER_AGENT']) );

if( IS_USING_TOKEN )
{
	if( isset($_SERVER['HTTP_X_CORDLESS_TOKEN']) )
		$token = $_SERVER['HTTP_X_CORDLESS_TOKEN'];
	elseif( isset($_GET['token']) )
		$token = $_GET['token'];
	else
		$token = null;

	if( strlen($token) != 32 ) ### If session id set fails, generate new
	{
		$token = md5(time() . '+01+1ur9m1u09cj1m049un1');
		header("X-Cordless-Token: " . $token);
	}

	session_id($token);
}
else
{
	header("Content-Type: text/plain");
}

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

	### Always be $User:ing!
	if( !isset($_SESSION['User']) || !$_SESSION['User'] instanceof User )
		$_SESSION['User'] = new User();

	$User = $_SESSION['User'];


	if( $keepSession !== true )
		session_write_close();


	if( $requireLogin !== false )
	{
		ensureLogin($User);

		if( IS_USING_TOKEN )
		{
			if( !isset($_GET['sig']) )
				throw New APIException("Signature Missing");

			ensureSignature($User, $_GET['sig']);
		}
	}

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
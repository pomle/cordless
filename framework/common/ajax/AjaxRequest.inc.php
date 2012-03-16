<?
namespace Asenine;

class AjaxRequestException extends \Exception
{}

function exception_error_handler($errno, $errstr, $errfile, $errline )
{
	switch($errno)
	{
		### Ignore these
		case E_USER_NOTICE:
		case E_NOTICE:
		case E_STRICT:
		case E_DEPRECATED:
		case E_WARNING:
			return false;
	}

	throw New \ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler("\Asenine\exception_error_handler");

try
{
	$action = $result = $call = null;

	DB::transactionStart();

	try
	{
		$protocol = preg_replace('/[^A-Za-z0-9+]/', '_', $_GET['protocol']);
		$action = $_GET['action'];

		$include = DIR_AJAX_IO . $protocol . '.io.php';

		if( !file_exists($include) )
			throw new \Exception(DEBUG ? sprintf('File Missing: "%s"', $include) : 'AjaxRequest Failed');

		require $include;
	}
	catch(AjaxRequestException $e)
	{
		\Message::addError($e->getMessage());
	}
	catch(\ErrorException $e)
	{
		### Catches any errors in PHP
		throw New Exception(
			DEBUG
			? sprintf('PHP Error (%u) "%s" at line %u in %s', $e->getSeverity(), $e->getMessage(), $e->getLine(), $e->getFile())
			: sprintf('PHP Error: %s', $e->getMessage())
		);
	}

	DB::transactionCommit();
}
catch(\Exception $e) ### Any uncaught exception will trickle down here
{
	$action = 'error';
	DB::transacationRollback();
	\Message::addError($e->getMessage());
}

\Message::asJSON($action, $result, $call);
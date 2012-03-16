<?
namespace Cordless;

require '../../Init.Application.inc.php';

class PanelException extends \Exception
{}

try
{
	session_start();

	if( !isset($_SESSION['User']) || !$_SESSION['User'] instanceof User )
		throw new PanelException(_("Session lost.") . ' <a href="/Login.php">' . _("Go to Login") . ' &raquo;</a>');

	$User = $_SESSION['User'];

	session_write_close();

	if( !isset($_GET['type']) )
		throw New PanelException('Panel type not specified');

	if( !isset($_GET['name']) )
		throw New PanelException('Panel name not specified');


	loadPanel($_GET['type'], $_GET['name'], isset($_GET['title']) ? $_GET['title'] : '');
}
catch(PanelException $e)
{
	echo
		Element\Library::head(_('Error')),
		$e->getMessage();
}
catch(\Exception $e)
{
	echo DEBUG ? $e->getMessage() : _("Error Updating Panel");
}

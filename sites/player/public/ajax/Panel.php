<?
namespace Cordless;

require '../../Init.Application.inc.php';

session_start();
require DIR_SITE_SYSTEM . 'init/User.inc.php';
session_write_close();

class PanelException extends \Exception
{}

try
{
	if( !$User->isLoggedIn() )
		throw new PanelException(_("Session lost.") . sprintf(' <a href="%s">%s</a> &raquo;', URL_LOGIN, _("Go to Login")));

	if( !isset($_GET['type']) )
		throw new PanelException('Panel type not specified');

	if( !isset($_GET['name']) )
		throw new PanelException('Panel name not specified');


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
	header("HTTP/1.1 500 Internal Server Error");
	echo DEBUG ? $e->getMessage() : _("Error Updating Panel");
}

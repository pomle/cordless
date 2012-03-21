<?
namespace Cordless;

define('NO_LOGIN', true);

require '../Init.Web.inc.php';

$User->logout();

unset($_SESSION['User']);

#header('Location: /');
echo Element\Page\Message::notice(
	_("Good Bye =("),
	_("You have been successfully logged out.") . ' ' . sprintf('<a href="./Login.php">%s</a>', htmlspecialchars(_("Login? =)")))
);
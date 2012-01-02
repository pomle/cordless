<?
define('NO_LOGIN_REQUIRED', true);

require '../Init.inc.php';

\User::logout();

header('Location: /');
<?
require '../Init.inc.php';

$User->logout();

unset($_SESSION['User']);

header('Location: /');
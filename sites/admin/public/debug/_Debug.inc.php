<?
require '../../Init.inc.php';

header("Content-type: text/plain; charset=UTF-8");

define('TIME_START', microtime(true));

function elapsedTime()
{
	printf("%.4F\n", microtime(true) - TIME_START);
}
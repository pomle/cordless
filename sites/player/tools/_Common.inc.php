<?
require __DIR__ . '/../Init.Application.inc.php';

function yes($a)
{
	$a = trim($a);
	return (strlen($a) == 1 && strpos('Yy', $a) !== false);
}

function no($a)
{
	$a = trim($a);
	return (strlen($a) == 1 && strpos('Nn', $a) !== false);
}
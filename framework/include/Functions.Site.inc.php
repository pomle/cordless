<?
/*
	Common functions specific for publicly accessible scripts
*/

$css = $js = array();
$addToHead = $addToFoot = array();

function addCSS()
{
	global $css;
	$css = array_merge($css, func_get_args());
}

function addJS()
{
	global $js;
	$js = array_merge($js, func_get_args());
}

function displayCSS()
{
	global $css;
	foreach($css as $url)
		printf('<link href="%s" type="text/css" rel="stylesheet">', $url);

	$css = array();
}

function displayJS()
{
	global $js;
	foreach($js as $url)
		printf('<script src="%s" type="text/javascript"></script>', $url);

	$js = array();
}


function addToHead($string)
{
	global $addToHead;
	$addToHead[] = $string;
}

function displayHead()
{
	global $addToHead;
	foreach($addToHead as $string)
		echo $string;
}

function addToFoot($string)
{
	global $addToFoot;
	$addToFoot[] = $string;
}

function displayFoot()
{
	global $addToFoot;
	foreach($addToFoot as $string)
		echo $string;
}
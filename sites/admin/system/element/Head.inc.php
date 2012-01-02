<meta http-equiv="Content-Type" content="text/html; charset=utf-8;">
<?
function displayCSS()
{
	global $css;

	$css = array_unique($css);
	foreach($css as $index => $url)
	{
		?><link rel="stylesheet" media="screen" href="<? echo $url; ?>" type="text/css"><?
		unset($css[$index]);
	}
}

function displayJS()
{
	global $js;

	$js = array_unique($js);
	foreach($js as $index => $url)
	{
		?><script src="<? echo $url; ?>" type="text/javascript"></script><?
		unset($js[$index]);
	}
}

displayCSS();
#displayJS(); ### All JS moved to bottom
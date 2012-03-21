<?
global $baseHref;
?><!DOCTYPE html>
<html lang="en">
<head>
	<base href="<? echo $baseHref; ?>">
	<?
	if( isset($pageTitle) ) printf('<title>%s</title>', htmlspecialchars($pageTitle));

	foreach($css as $path)
		printf('<link rel="stylesheet" type="text/css" href="%s">', $path);

	?>
	<link href="http://public.pomle.com/Cordless-Interface-NowPlaying-Nero.png" rel="image_src">
</head>

<body>

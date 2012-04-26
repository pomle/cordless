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

	if( isset($imageURL) )
		printf('<link rel="image_src" href="%s">', htmlspecialchars($imageURL));
	?>
</head>

<body>

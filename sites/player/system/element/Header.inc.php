<?
function makePlayer()
{
	$Player = new \Element\Cordless();
	return $Player;
}

$Player = makePlayer();
?><!DOCTYPE html>
<html lang="en">
<head>
	<?
	if( isset($pageTitle) ) printf('<title>%s</title>', htmlspecialchars($pageTitle));

	foreach($css as $path)
		printf('<link rel="stylesheet" type="text/css" href="%s">', $path);

	?>
	<meta name="viewport" content="width=400">
</head>

<body>
	<header id="control">
		<div class="inner">
			<form action="/ajax/Panel.php?type=Library&amp;name=Search" method="get" id="search">
				<div class="search">
					<input type="text" name="q" value="<? if( isset($_GET['q']) ) echo htmlspecialchars($_GET['q']); ?>">
				</div>
			</form>

			<div class="torus">
				<a href="/"><img src="/img/SiteLogo.png" alt="Re:Cordless"></a>

				<nav>
					<ul>
						<li><a href="#Upload" class="panelLibrary"><? echo _("Upload"); ?></a></li>
						<li><a href="#SmartPlaylists" class="panelLibrary"><? echo _("SmartPlaylists"); ?></a></li>
					</ul>
				</nav>
			</div>

			<?
			echo $Player;
			?>
		</div>
	</header>
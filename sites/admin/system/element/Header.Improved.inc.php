<?
global $pageTitle, $pageSubtitle, $userPanel;

header('Content-type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html>
	<head>
		<title><?
			echo htmlspecialchars($pageTitlePrefix);
			if( isset($pageTitle) ) { echo ': ', htmlspecialchars($pageTitle); }
		?></title>
		<?
		require DIR_ADMIN_ELEMENT . 'Head.inc.php';
		?>
	</head>
	<body>
		<div id="cranium"></div>
		<div id="populus"></div>

		<header class="header">
			<?
			if( is_array($userPanel) )
			{
				?>
				<div class="userPanel">
					<? echo join(' ', $userPanel); ?>
				</div>
				<?
			}
			?>

			<div class="titles">
				<a href="/" class="logo"><img src="/layout/SiteLogo.png" alt=""></a>

				<? if($pageTitle) echo '<h1>'.htmlspecialchars($pageTitle).'</h1>'; ?>
				<? if($pageSubtitle) echo '<h2>'.htmlspecialchars($pageSubtitle).'</h2>'; ?>
			</div>

		</header>

		<nav class="menu">
			<?
			require DIR_ADMIN_INCLUDE . 'MainMenu.inc.php';
			displayMenu();
			?>
		</nav>

		<section class="pageContent">
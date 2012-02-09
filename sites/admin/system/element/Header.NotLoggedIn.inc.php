<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?
			echo htmlspecialchars($pageTitlePrefix);
			if( isset($pageTitle) ) { echo ': ', htmlspecialchars($pageTitle); }
		?></title>
		<?
		require DIR_ADMIN_ELEMENT . 'Head.inc.php';
		?>
		<style type="text/css">
			fieldset {
				width: 30em;
				margin: 5em auto;
				padding: 1em;
			}

			fieldset form {	font-size: 110%; }
			fieldset input { padding: .3em; }

			table { margin: 0 auto; }
			table td { padding: .4em; }
			table td.control { text-align: center; }
		</style>
	</head>
	<body>
		<fieldset>
			<legend><? echo htmlspecialchars($title); ?></legend>

			<? Message::displayElements(); ?>
	<footer class="footer">
	</footer>
	<?
	foreach($js as $path)
		printf('<script type="text/javascript" src="%s"></script>', $path);
	?>
</body>
</html>
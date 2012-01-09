<?
require __DIR__ . '/_Init.inc.php';

function parseTree($path)
{
	$files = recGlob('*.php', $path);
	return parseFiles($files, $path);
}

function parseFiles($files, $basePath = '/', $policy = null)
{
	$items = array();
	foreach($files as $file)
	{
		if( $path = parseContent(file_get_contents($file), str_replace($basePath, '/', $file),  $policy) )
			$items[] = $path;
	}
	return $items;
}

function parseContent($content, $href = null, $policy = null)
{
	if( !preg_match('%^#MENUPATH:(.+)$%m', $content, $match) ) ### If not set, we shouldn't add this file to menu
		return false;

	$path = trim($match[1]);

	if( preg_match('%^#URLPATH:(.+)$%m', $content, $match) )
		$href = trim($match[1]);

	$content = preg_replace('%(//|#).*%', '', $content); ### Strip away PHP line comments

	if( preg_match('%define\([\'\"]ACCESS_POLICY[\'\"], ?[\'\"]([A-Za-z]+)[\'\"]\);%m', $content, $match) )
		$policy = trim($match[1]);

	return array('path' => $path, 'href' => $href, 'policy' => $policy);
}

$Menu = new \Element\MainMenu();

$items = parseTree(DIR_ADMIN_PUBLIC);

uasort($items, function($a, $b) { return $a['path'] > $b['path']; });

foreach($items as $item)
	$Menu->addItem($item['path'], $item['href'], $item['policy']);

$content = "<?\n\$tree = " . $Menu->getAsVariable() . ";";

echo $content;

file_put_contents(DIR_ADMIN_CONFIG . 'MenuTree.inc.php', $content);
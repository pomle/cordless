<?
require __DIR__ . '/_Init.inc.php';

function parseFile($file)
{
	return parseContent(file_get_contents($file));
}

function parseContent($content)
{
	if( !preg_match('%^#MENUPATH:(.+)$%m', $content, $match) ) return false;
	$path = trim($match[1]);

	if( !preg_match('%^#URLPATH:(.+)$%m', $content, $match) ) return false;
	$href = trim($match[1]);

	if( preg_match('%define\([\'\"]ACCESS_POLICY[\'\"], ?[\'\"]([A-Za-z]+)[\'\"]\);%m', $content, $match) )
		$policy = trim($match[1]);
	else
		$policy = null;

	return array('path' => $path, 'href' => $href, 'policy' => $policy);
}

$Menu = new \Element\MainMenu();

$files = glob(DIR_ADMIN_PUBLIC . '*.php');
$items = array();

foreach($files as $file)
{
	if( !$item = parseFile($file) ) continue;

	$items[$item['path']] = $item;
}

ksort($items);

foreach($items as $path => $item)
	$Menu->addItem($path, $item['href'], $item['policy']);

$content = "<?\n\$tree = " . $Menu->getAsVariable() . ";";

echo $content;

file_put_contents(DIR_ADMIN_CONFIG . 'MenuTree.inc.php', $content);
<?
require '_Debug.php';

$url = $_GET['url'];


$doc = new DOMDocument();
@$doc->loadHTMLFile($url);

$xpath = new DOMXpath($doc);

echo $xpath->query("//meta [@name='title']")->item(0)->getAttribute('content');


$nodelist = $xpath->query("//link[@rel='canonical']");

var_dump($nodelist);

if( $nodelist->length > 0 )
{
	$node = $nodelist->item(0);

	$canonical = $node->getAttribute('href');

	if( preg_match('/v=([A-Za-z0-9\-]+)/', $canonical, $match) )
	{
		$url = sprintf('http://www.youtube.com/get_video?video_id=%s', $match[1]);

		echo $url;
	}
}
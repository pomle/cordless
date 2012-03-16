<?
require '_Debug.php';

header("Content-Type: text/html; charset=utf-8");

$xml = simplexml_load_string('<lfm status="failed">
    <error code="10">Invalid API Key</error>
</lfm>');

$a = $xml->xpath('/lfm/error');
echo $a[0];
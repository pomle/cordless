<?
require '_Debug.inc.php';

$Archive = new \Archive('media');

print_r($Archive);

echo $Archive->getFileName(md5('ABCDEFGHIJKLMNOPQRSTUVWXYZ'));
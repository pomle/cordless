<?
require '_Debug.php';


$_SERVER['HTTP_RANGE'] = 'bytes=11525-';


$offset = 0;
$length = $fileSize = filesize($fileName);

if( isset($_SERVER['HTTP_RANGE']) && preg_match('%bytes=(\d+)-(\d+)?%i', $_SERVER['HTTP_RANGE'], $match) )
{
	$offset = (int)$match[1];
	if( isset($match[2]) ) $length = (int)$match[2];
}

$dataSize = (int)$length - $offset + 1;

var_dump($offset, $length, $dataSize);

#$data_size = intval($range[1]) - intval($range[0]) + 1;
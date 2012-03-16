<?
require '_Debug.php';

$File = new \Asenine\File('/home/pom/She - nova.mp3');

print_r($File);

$res = \Asenine\Media\Type\Audio::canHandleFile($File);

var_dump($res);

$Audio = \Asenine\Media\Type\Audio::createFromFile($File);

print_r($Audio);

$Track = \Cordless\Event\Track::createFromAudio($Audio);

print_r($Track);
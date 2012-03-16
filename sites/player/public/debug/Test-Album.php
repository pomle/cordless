<?
require '_Debug.php';

$Album = \Music\Album::loadFromDB(2);

print_r($Album);

die();

$Audio = \Manager\Media::loadOneFromDB(18282);

print_r($Audio);

\Event\Track::createManual($Audio, 'Pomle', 'Freakshow', 'Freakazoidee', '2010', '1');
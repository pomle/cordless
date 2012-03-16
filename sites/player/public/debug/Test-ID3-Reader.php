<?
require '_Debug.php';

require DIR_SITE_SYSTEM . 'libs/getid3/getid3.php';

#if( $Track = \Music\Track::loadFromDB(90) )

$file = '/home/pom/joe.mp3';

#$getID3 = new getID3();

#$id3 = $getID3->analyze($file);

$ID3 = new ID3($file);

$id3 = $ID3->meta;

var_dump($id3);



#print_r($ID3->id3);
var_dump( $ID3->getTitle(), $ID3->getArtist(), $ID3->getAlbum(), $ID3->getYear(), $ID3->getTrackNumber() );

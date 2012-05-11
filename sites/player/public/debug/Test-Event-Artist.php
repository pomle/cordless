<?
require '_Debug.php';

$artists = \Cordless\Artist::loadByName('Infected Mushroom');

var_dump($artists);

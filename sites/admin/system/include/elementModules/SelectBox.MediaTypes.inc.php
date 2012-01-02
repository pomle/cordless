<?
$noneSelectable = (isset($args[1]) && $args[1] == true);

$mediaTypes = array();

if( $noneSelectable )
	$mediaTypes += array(0 => 'Auto Detect');

$mediaTypes += \Manager\Dataset\Media::getTypes();

$Select = new \Element\SelectBox($args[0] ?: 'mediaType', $args[2]);
$Select->addItemsFromArray($mediaTypes);

echo $Select;
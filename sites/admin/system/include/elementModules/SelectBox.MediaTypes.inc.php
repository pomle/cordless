<?
$noneSelectable = (isset($args[1]) && $args[1] == true);

$mediaTypes = array();

if( $noneSelectable )
	$mediaTypes += array(0 => 'Auto Detect');

$mediaTypes += \Asenine\Media\Dataset::getTypes();

$Select = new \Asenine\Element\SelectBox(isset($args[0]) ? $args[0] : 'mediaType', isset($args[2]) ? $args[2] : null);
$Select->addItemsFromArray($mediaTypes);

echo $Select;
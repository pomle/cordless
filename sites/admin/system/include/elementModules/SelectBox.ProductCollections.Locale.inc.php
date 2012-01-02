<?
$localeID = $args[0];

$query = \DB::prepareQuery("SELECT
		pc.ID
	FROM
		ProductCollections pc
	WHERE
		pc.localeID = %u",
	$localeID
);

$productCollectionIDs = \DB::populate($query);
$productCollections = \Manager\Product\Collection::loadFromDB($productCollectionIDs);

$Select = new \Element\SelectBox($args[1] ?: 'productCollectionID',$args[2] ?: null);
$Select->addItemsFromArrayOfObjects($productCollections, 'title');
asort($Select->items, SORT_LOCALE_STRING);
echo $Select;
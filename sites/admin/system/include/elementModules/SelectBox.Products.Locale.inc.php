<?
$localeID = $args[0];

$query = \DB::prepareQuery("SELECT
		pl.productID,
		pl.title
	FROM
		ProductsLocale pl
	WHERE
		pl.isEnabled = 1
		AND pl.localeID = %u",
	$localeID
);

$products = \DB::populate($query);

$Select = new \Element\SelectBox($args[1] ?: 'productID');
$Select->addItemsFromArray($products);
asort($Select->items, SORT_LOCALE_STRING);
echo $Select;
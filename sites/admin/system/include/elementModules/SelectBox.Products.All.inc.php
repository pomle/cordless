<?
$localeID = $args[0];

$query = \DB::prepareQuery("SELECT
		p.ID AS productID,
		IFNULL(pl.title, '[N/A in your Locale]') AS title
	FROM
		Products p
		LEFT JOIN ProductsLocale pl ON pl.productID = p.ID AND pl.localeID = %u",
	$localeID);

$products = \DB::populate($query);

$Select = new \Element\SelectBox($args[1] ?: 'productID');
$Select->addItemsFromArray($products);
asort($Select->items, SORT_LOCALE_STRING);
echo $Select;
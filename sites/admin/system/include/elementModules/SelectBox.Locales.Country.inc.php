<?
$query = "SELECT
		ID,
		country
	FROM
		Locales";


$locales = \DB::queryAndFetchArray($query);

$Select = new \Element\SelectBox($args[0] ?: 'localeID');
$Select->addItemsFromArray($locales);
asort($Select->items, SORT_LOCALE_STRING);
echo $Select;
<?
$query = "SHOW COLLATION";
$result = \DB::queryAndFetchResult($query);
while($row = DB::assoc($result))
{
	$collations[$row['Collation']] = $row['Collation'];
}

$Select = new \Element\SelectBox(isset($args[0]) ? $args[0] : 'collation', 'utf8_general_ci');
$Select->addItemsFromArray($collations);
asort($Select->items, SORT_LOCALE_STRING);
echo $Select;
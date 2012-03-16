<?
$query = "SHOW COLLATION";
$result = \Asenine\DB::queryAndFetchResult($query);
while($row = \Asenine\DB::assoc($result))
{
	$collations[$row['Collation']] = $row['Collation'];
}

$Select = new \Asenine\Element\SelectBox(isset($args[0]) ? $args[0] : 'collation', 'utf8_general_ci');
$Select->addItemsFromArray($collations);
asort($Select->items, SORT_LOCALE_STRING);
echo $Select;
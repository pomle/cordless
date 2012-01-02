<?
$localeID = $args[0];

$query = \DB::prepareQuery("SELECT b.ID, m.fileOriginalName , b.linkURL
			FROM Bulletins b 
			LEFT JOIN Media m ON m.ID = b.mediaID 
			WHERE isActive = 1 
			AND b.label = '470x220'
			AND b.localeID = %u",
	$localeID
);
$result = \DB::queryAndFetchResult($query);
while($row = DB::assoc($result))
{
	$collations[$row['ID']] = $row['fileOriginalName'] . " - ( ".$row['linkURL']." )";
}

$Select = new \Element\SelectBox($args[1] ?: 'BulletinID',$args[1] ?: Null);
$Select->addItemsFromArray($collations);
asort($Select->items, SORT_LOCALE_STRING);
echo $Select;
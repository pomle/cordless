<?
$itemIDs = empty($args[0]) ? getAllItemIDs() : (array)$args[0];

foreach($itemIDs as $itemID)
{
	$cacheKeys[] = sprintf('ITEM_%u', $itemID);
}
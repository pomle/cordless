<?
$name = $args[0];

$query = "SELECT
		sp.ID AS stockPositionID,
		CONCAT_WS(' - ', sp.readableName, sp.longName) AS label
	FROM
		StockPositions sp
	ORDER BY
		readableName ASC";

$stockPositions = \DB::populate($query);

$Select = new \Element\SelectBox($name ?: 'stockPositionID');
$Select->addItem('');
$Select->addItemsFromArray($stockPositions);

echo $Select;
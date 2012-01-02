<?
namespace Element\Antiloop;

defaultSort($params, 'timeCreated', true);

$Stmt = new \Query\Select("SELECT
		sbip.ID AS securityBlockedIPID,
		sbip.timeCreated,
		sbip.isEnabled,
		sbip.hostAddress
	FROM
		SecurityBlockedIPs sbip");

if( $filter['search'] )
{
	$Stmt->addHaving('hostAddress LIKE %S', $filter['search']);
	$Antiloop->addNotice(sprintf(_('Sökning på IP-adress: "%s"'), $filter['search']));
}

$Antiloop
	->setDataset($Stmt)
	->addFilters
	(
		Filter\Search::text()
	)
	->addFields
	(
		Field::id('securityBlockedIPID'),
		Field::enabled('isEnabled'),
		Field::time('timeCreated'),
		Field::ip('hostAddress')
	);
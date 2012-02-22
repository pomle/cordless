<?
namespace Element\Antiloop;

defaultSort($params, 'timeCreated', true);

$Stmt = new \Query\Select("SELECT
		usip.ID AS userSecurityIPID,
		usip.policy,
		usip.spanStart,
		(usip.spanStart + usip.spanAppend) AS spanEnd
	FROM
		UserSecurityIPs usip");

if( isset($filter['search']) && strlen($filter['search']) )
{
	$Stmt->addHaving('%u BETWEEN spanStart AND spanEnd', $filter['search']);
	$Antiloop->addNotice(sprintf(_('Sökning på IP-adress: "%s"'), $filter['search']));
}


$policyMap = \Dataset\User::getSecurityIPTypeMap();

$Antiloop
	->setDataset($Stmt)
	->addFilters
	(
		Filter\Search::text()
	)
	->addFields
	(
		Field::id('userSecurityIPID'),
		Field\Map::icon('policy', _('Policy'), 'shield', $policyMap),
		Field::ip('spanStart', _('IP-spann Börjar'), 'computer_key'),
		Field::ip('spanEnd', _('IP-spann Slutar'), 'computer_go')
	);
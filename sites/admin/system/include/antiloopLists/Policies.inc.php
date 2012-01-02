<?
namespace Element\Antiloop;

defaultSort($params, 'policy', false);

$Stmt = new \Query\Select("SELECT ID AS policyID, policy, description FROM Policies");

if( $filter['search'] )
{
	$search = $filter['search'];
	$Stmt->addWhere('policy LIKE %S', str_replace(' ', '%', $search));
	search($Antiloop, $search);
}

$Antiloop
	->setDataset($Stmt)
	->addFilters
	(
		Filter\Search::text(),
		Filter\Slice::pagination()
	)
	->addFields
	(
		Field::id('policyID'),
		//Field::enabled('isEnabled'),
		Field::text('policy', _('Benämning'), 'key'),
		Field::text('description', _('Beskrivning'), 'comment')
		//Field::examiner('/PolicyView.php', array('policyID'))
	);
<?
namespace Element\Antiloop;

defaultSort($params, 'timeLastLogin', true);

$Stmt = new \Query\Select("SELECT
		ug.ID AS userGroupID,
		ug.name,
		(SELECT COUNT(*) FROM UserGroupPolicies WHERE userGroupID = ug.ID) AS countPolicies,
		(SELECT COUNT(*) FROM UserGroupUsers WHERE userGroupID = ug.ID) AS countUsers
	FROM
		UserGroups ug");

if( $filter['search'] )
{
	$search = $filter['search'];

	if( is_numeric($search) )
	{
		$Stmt->addWhere('ug.ID = %u', $search);
		point($Antiloop, $search);
	}
	elseif( $search[0] == '@' )
	{
		$search = substr($search, 1);

		$comp = str_replace('*', '%', $search);

		$query = \DB::prepareQuery("SELECT DISTINCT
				ugu.userGroupID
			FROM
				UserGroupUsers ugu
				JOIN Users u ON u.ID = ugu.userID
			WHERE
				u.username LIKE %S
				OR u.fullname LIKE %S",
			$comp, $comp);

		$userGroupIDs = \DB::queryAndFetchArray($query);

		$Stmt->addWhere('ug.ID IN %a', $userGroupIDs);
		$Antiloop->addNotice(sprintf(_('Sökning på användare: "%s"'), $search));
	}
	else
	{
		$Stmt->addWhere('name LIKE %S', str_replace(' ', '%', $search));
		search($Antiloop, $search);
	}
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
		Field::id('userGroupID'),
		Field::text('name', _('Benämning')),
		Field::count('countPolicies', _('Rättigheter'), 'application_key'),
		Field::count('countUsers', _('Användare'), 'group'),
		Field::creator('/UserGroupEdit.php', array('userGroupID'))
	);
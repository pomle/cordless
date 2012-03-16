<?
namespace Element\Antiloop;

defaultSort($params, 'timeLastLogin', true);

$Stmt = new \Query\Select("SELECT
		ID AS userID,
		isEnabled,
		isAdministrator,
		username,
		fullname,
		timeLastLogin,
		ROUND(timeAutoLogout / 60) AS timeAutoLogout,
		countLoginsSuccessful,
		countLoginsFailed
	FROM Users");

if( isset($filter['search']) && $filter['search'] )
{
	$search = $filter['search'];

	if( is_numeric($search) )
	{
		$Stmt->addWhere('ID = %u', $search);
		point($Antiloop, $search);
	}
	else
	{
		$search = str_replace(' ', '%', $search);
		$Stmt->addWhere('(username LIKE %S OR fullname LIKE %S)', $search, $search);
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
		Field::id('userID'),
		Field::enabled('isEnabled', _('Aktiv (kan logga in)'), 'key_go'),
		Field::text('username', _('Användarnamn'), 'user'),
		Field::text('fullname', _('Namn'), 'vcard'),
		Field::time('timeLastLogin', _('Senaste inloggning'), 'time'),
		Field\Number::integer('timeAutoLogout', _('Autologout'), 'time_go'),
		Field::count('countLoginsSuccessful', _('Lyckade inloggningar'), 'key_add'),
		Field::count('countLoginsFailed', _('Misslyckade inloggningar'), 'key_delete'),
		Field::creator('/UserEdit.php', array('userID'))
	);

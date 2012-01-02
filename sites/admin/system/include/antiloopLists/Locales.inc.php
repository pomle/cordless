<?
namespace Element\Antiloop;

defaultSort($params, 'localeID', false);

$Stmt = new \Query\Select("SELECT
		ID AS localeID,
		isEnabled,
		ident,
		country,
		language,
		currency,
		domain,
		locale,
		collation
	FROM Locales");

$Antiloop
	->setDataset($Stmt)
	->addFields
	(
		Field::id('localeID'),
		Field::locale(),
		Field::enabled('isEnabled'),
		Field::text('ident', _('Ankring'), 'anchor'),
		Field::text('country', _('Land'), 'world'),
		Field::text('language', _('Land'), 'page_white_world'),
		Field::text('currency', _('Valuta'), 'money_euro'),
		Field::text('domain', _('Domän'), 'lightning_go'),
		Field::text('locale', _('Lokaliseringskod'), 'keyboard'),
		Field::text('collation', _('Sortering'), 'keyboard')
	);
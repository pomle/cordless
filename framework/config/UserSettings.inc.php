<?
$currencyOptions = array(
	'%i' => money_format('%i', 123456.789),
	'%n' => money_format('%n', 123456.789),
	'%!i' => money_format('%!i', 123456.789),
	//'%!n' => money_format('%!n', 123456.789),
	'%^!i' => money_format('%^!i', 123456.789),
	//'%^!n' => money_format('%^!n', 123456.789),
);

$userSettings = array(
	'SaveViewStateBetweenSessions' => array(
		'description' => _('Spara mina visningsinställningar mellan inloggningar'),
		'type' => 'boolean',
		'default' => true
	),

	'SaveViewStateBetweenPageViews' => array(
		'description' => _('Spara mina visningsinställningar mellan sidvisningar'),
		'type' => 'boolean',
		'default' => true
	),

	'DisplayLocaleID' => array(
		'description' => _('Anpassa språk, tidvisning etc. efter'),
		'type' => 'selector',
		'values' => array(0 => _('Internationell')) + \Manager\Dataset\Locale::getCountries(),
		'default' => 0
	),

	'DefaultLocaleID' => array(
		'description' => _('Standardval för språk'),
		'type' => 'selector',
		'values' => array(0 => '-') + \Manager\Dataset\Locale::getCountries(),
		'default' => 0
	),

	'DefaultCurrencyLocaleID' => array(
		'description' => _('Standardval för valuta'),
		'type' => 'selector',
		'values' => array(0 => '-') + \Manager\Dataset\Locale::getCountries(),
		'default' => 0
	),

	'DefaultTimeFormat' => array(
		'description' => _('Visning för tid'),
		'type' => 'selector',
		'values' => array(
			'%Y-%m-%d %H:%M' => strftime('%Y-%m-%d %H:%M', time()),
			'%Y-%m-%d %H:%M:%S' => strftime('%Y-%m-%d %H:%M:%S', time()),
			'%c' => strftime('%c', time()),
			'%s' => strftime('Unix Timestamp: %s', time())
		),
		'default' => '%Y-%m-%d %H:%M'
	),

	'DefaultDateFormat' => array(
		'description' => _('Visning för datum'),
		'type' => 'selector',
		'values' => array(
			'%Y-%m-%d' => strftime('%Y-%m-%d', time()),
			'%F' => strftime('%F', time()),
			'%D' => strftime('%D', time()),
			'%x' => strftime('%x', time())
		),
		'default' => '%Y-%m-%d'
	),

	'DefaultMoneyFormat' => array(
		'description' => _('Visning för valutor'),
		'type' => 'selector',
		'values' => $currencyOptions,
		'default' => '%i'
	),

	'DefaultMoneyFormatShort' => array(
		'description' => _('Visning för valörer'),
		'type' => 'selector',
		'values' => $currencyOptions,
		'default' => '%!i'
	)
);

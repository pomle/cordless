<?
$status = 'notice';

interport('action', 'localeID');

switch($action)
{
	case 'save':
		ensurePolicies('AllowEditLocale');
		unset($locale); // Otherwise it will not be overwritten by interport
		interport('isEnabled', 'ident', 'country', 'language', 'currency', 'domain', 'locale', 'collation', 'conversionRate', 'mailName', 'mailAddress', 'timestampFormat', 'dateFormat', 'timeFormat', 'moneyFormat', 'moneyFormatShort', 'customerServiceEmail', 'customerServicePhone');

		$conversionRate = str_replace(',', '.', $conversionRate);

		$previousLocale = setlocale(LC_ALL, $locale);
		if( $previousLocale != $locale ) { Message::addAlert(sprintf(_('Lokaliseringskod "%s" är ej installerad'), $locale)); }
		setlocale(LC_ALL, $previousLocale);

		if( !$localeID )
		{
			ensurePolicies('AllowCreateLocale');
			$query = \DB::prepareQuery("INSERT INTO Locales (ID) VALUES(NULL)");
			$localeID = \DB::queryAndGetID($query);
			Message::addNotice(MESSAGE_ROW_CREATED);
		}

		$query = DB::prepareQuery("UPDATE Locales SET isEnabled = %d, ident = '%s', country = '%s', language = '%s', currency = '%s', domain = '%s', locale = '%s', collation = '%s', conversionRate = %F, mailName = '%s', mailAddress = '%s', timestampFormat = '%s', dateFormat = '%s', timeFormat = '%s', moneyFormat = '%s', moneyFormatShort = '%s', customerServiceEmail = '%s', customerServicePhone = '%s' WHERE ID = %d", $isEnabled, $ident, $country, $language, $currency, $domain, $locale, $collation, $conversionRate, $mailName, $mailAddress, $timestampFormat, $dateFormat, $timeFormat, $moneyFormat, $moneyFormatShort, $customerServiceEmail, $customerServicePhone, $localeID);
		DB::queryAndCountAffected($query);
		Message::addNotice(MESSAGE_ROW_UPDATED);


	case 'load':
		ensurePolicies('AllowViewLocale');
		$query = DB::prepareQuery("SELECT *, ID as row, ID as localeID FROM Locales WHERE ID = %d", $localeID);
		$result = DB::assoc(DB::queryAndFetchResult($query));
		break;

	case 'delete':
		ensurePolicies('AllowDeleteLocale');
		if(!DB::pick(DB::prepareQuery("SELECT COUNT(*) FROM Locales WHERE ID = %d", $localeID))) throw New Exception(MESSAGE_ROW_MISSING);
		$query = DB::prepareQuery("DELETE FROM Locales WHERE ID = %d", $localeID);
		DB::queryAndCountAffected($query);
		Message::addNotice(MESSAGE_ROW_DELETED);
		break;

	case 'new':
		break;

	default:
		throw New Exception(MESSAGE_INVALID_ACTION.' ('.$action.')');
		break;
}
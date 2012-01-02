<?
namespace Element\Antiloop;

function articleFilter(\Element\Antiloop $Antiloop, $string)
{
	if( strlen($string) == 0 ) return array();

	if( is_numeric($string) )
		$articleIDs = array($string);
	else
		$articleIDs = \Manager\Dataset\Article::getBySearch($string);

	$Antiloop->addNotice(sprintf(_('Sökning på artikel: "%s"'), $string));

	return $articleIDs;
}

function defaultSort(array &$params, $field, $isReversed = false)
{
	if( !isset($params['sort']) ) $params['sort'] = $field;
	if( !isset($params['sortReverse']) ) $params['sortReverse'] = (bool)$isReversed;
}

function localize($Antiloop, $localeID)
{
	$localeID = (int)$localeID;
	$countries = \Manager\Dataset\Locale::getCountries();
	if( isset($countries[$localeID]) )
	{
		$Antiloop->addNotice(sprintf(_('Land: %s'), $countries[$localeID]));
	}
}

function langlize($Antiloop, $localeID)
{
	$localeID = (int)$localeID;
	$languages = \Manager\Dataset\Locale::getLanguages();
	if( isset($languages[$localeID]) )
	{
		$Antiloop->addNotice(sprintf(_('Språk: %s'), $languages[$localeID]));
	}
}

function point($Antiloop, $id)
{
	$Antiloop->addNotice(str_replace('%ID%', (int)$id, _('Pekar: %ID%')));
}

function positionFilter(\Element\Antiloop $Antiloop, $string)
{
	if( strlen($string) == 0 ) return array();

	if( is_numeric($string) )
		$positionIDs = array($string);
	else
		$positionIDs = \Manager\Dataset\StockPosition::getBySearch($string);

	$Antiloop->addNotice(sprintf(_('Sökning på position: "%s"'), $string));

	return $positionIDs;
}

function search($Antiloop, $string)
{
	$Antiloop->addNotice(str_replace('%STRING%', $string, _('Sök: "%STRING%"')));
}

function searchIgnored($Antiloop)
{
	$Antiloop->addNotice(_('Sökvärde ignorerat'));
}
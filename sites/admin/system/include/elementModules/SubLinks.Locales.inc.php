<?
$locales = \Manager\Dataset\Locale::getIdent();

$SubLinks = new \Element\SubLinks();
$SubLinks->icon = 'world';
$SubLinks->caption = _('Skifta Språk/Land');
foreach($locales as $localeID => $locale)
	$SubLinks->addLink(sprintf($args[0], $localeID), 'flags/' . $locale['ident'], $locale['country']);

echo $SubLinks;
<?
$locales = explode("\n", shell_exec('locale -a'));
$locales = array_combine($locales, $locales);

$Select = new \Element\SelectBox(isset($args[0]) ? $args[0] : 'locale');
$Select->addItemsFromArray($locales);
asort($Select->items, SORT_LOCALE_STRING);
echo $Select;
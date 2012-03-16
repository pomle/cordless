<?
#MENUPATH:Database/Language
define('ACCESS_POLICY', 'AllowViewLocale');

use \Asenine\Element\Input;

require '../Init.inc.php';

$pageTitle = _('Database');
$pageSubtitle = _('Language');

$LocaleList = \Element\Antiloop::getAsDomObject('Locales.Load');

$IOCall = new \Element\IOCall('Locale');
$Control = new \Element\IOControl($IOCall);
$Control
	->addButton(new \Element\Button\Clear())
	->addButton(new \Element\Button\Save())
	->addButton(new \Element\Button\Delete());

require HEADER;
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('world', _('Languages')); ?></legend>

	<? echo $LocaleList; ?>

	<? echo $IOCall->getHead(); ?>

	<div class="ajaxEdit">
		<input type="hidden" name="localeID">

		<fieldset>
			<legend><? echo \Element\Tag::legend('wrench_orange', _('Funktion')); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Aktiverad'), Input::checkbox('isEnabled'))
				->addRow(_('Ankring'), Input::text('ident')->size(6))
				->addRow(_('Land'), Input::text('country'))
				->addRow(_('Språk'), Input::text('language'))
				->addRow(_('Valuta'), Input::text('currency'))
				->addRow(_('Växlingskurs'), Input::text('conversionRate'))
				->addRow(_('Domän'), Input::text('domain'))
				->addRow(_('Lokaliseringskod'), new \Element\Module('SelectBox.SystemLocales'))
				->addRow(_('Bokstavsordning'), new \Element\Module('SelectBox.Collations'));
			?>
		</fieldset>

		<fieldset>
			<legend><? echo \Element\Tag::legend('email', _('E-post')); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Avsändarnamn'), Input::text('mailName')->size(40))
				->addRow(_('Avsändaradress'), Input::text('mailAddress')->size(40));
			?>
		</fieldset>

		<fieldset>
			<legend><? echo \Element\Tag::legend('vcard', _('Kontaktuppgifter')); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Kundtjänst E-post'), Input::text('customerServiceEmail')->size(32))
				->addRow(_('Kundtjänst Telefon'), Input::text('customerServicePhone')->size(32));
			?>
		</fieldset>

		<? echo $Control; ?>
	</div>

	<? echo $IOCall->getFoot(); ?>

</fieldset>
<?
require FOOTER;
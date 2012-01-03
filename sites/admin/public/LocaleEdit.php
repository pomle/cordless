<?
#MENUPATH:Databas/Språk
define('ACCESS_POLICY', 'AllowViewLocale');

require '../Init.inc.php';

$pageTitle = _('System');
$pageSubtitle = _('Språk');

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
	<legend><? echo \Element\Tag::legend('world', _('Språk')); ?></legend>

	<? echo $LocaleList; ?>

	<? echo $IOCall->getHead(); ?>

	<div class="ajaxEdit">
		<input type="hidden" name="localeID">

		<fieldset>
			<legend><? echo \Element\Tag::legend('wrench_orange', _('Funktion')); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Aktiverad'), \Element\Input::checkbox('isEnabled'))
				->addRow(_('Ankring'), \Element\Input::text('ident')->size(6))
				->addRow(_('Land'), \Element\Input::text('country'))
				->addRow(_('Språk'), \Element\Input::text('language'))
				->addRow(_('Valuta'), \Element\Input::text('currency'))
				->addRow(_('Växlingskurs'), \Element\Input::text('conversionRate'))
				->addRow(_('Domän'), \Element\Input::text('domain'))
				->addRow(_('Lokaliseringskod'), new \Element\Module('SelectBox.SystemLocales'))
				->addRow(_('Bokstavsordning'), new \Element\Module('SelectBox.Collations'));
			?>
		</fieldset>

		<fieldset>
			<legend><? echo \Element\Tag::legend('email', _('E-post')); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Avsändarnamn'), \Element\Input::text('mailName')->size(40))
				->addRow(_('Avsändaradress'), \Element\Input::text('mailAddress')->size(40));
			?>
		</fieldset>

		<fieldset>
			<legend><? echo \Element\Tag::legend('vcard', _('Kontaktuppgifter')); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Kundtjänst E-post'), \Element\Input::text('customerServiceEmail')->size(32))
				->addRow(_('Kundtjänst Telefon'), \Element\Input::text('customerServicePhone')->size(32));
			?>
		</fieldset>

		<? echo $Control; ?>
	</div>

	<? echo $IOCall->getFoot(); ?>

</fieldset>
<?
require FOOTER;
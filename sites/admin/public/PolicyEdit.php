<?
#MENUPATH:System/Rättigheter
define('ACCESS_POLICY', 'AllowViewPolicy');

require '../Init.inc.php';

$pageTitle = _('System');
$pageSubtitle = _('Rättigheter');

$PolicyList = \Element\Antiloop::getAsDomObject('Policies.Load');

$IOCall = new \Element\IOCall('Policy');
$Control = new \Element\IOControl($IOCall);
$Control
	->addButton(new \Element\Button\Clear())
	->addButton(new \Element\Button\Save())
	->addButton(new \Element\Button\Delete());

require HEADER;
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('key', _('Rättigheter')); ?></legend>

	<? echo $PolicyList; ?>

	<? echo $IOCall->getHead(); ?>

	<div class="ajaxEdit">
		<input type="hidden" name="policyID">

		<fieldset>
			<legend><? echo \Element\Tag::legend('wrench_orange', _('Funktion')); ?></legend>
			<?
			echo \Element\Table::inputs()
				//->addRow(_('Aktiverad'), \Element\Input::checkbox('isEnabled'))
				->addRow(_('Benämning'), \Element\Input::text('policy')->size(40))
				->addRow(_('Beskrivning'), \Element\Input::text('description')->size(80));
			?>
		</fieldset>

		<? echo $Control; ?>
	</div>

	<? echo $IOCall->getFoot(); ?>

</fieldset>
<?
require FOOTER;
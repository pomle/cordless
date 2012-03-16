<?
#MENUPATH:System/Policies
define('ACCESS_POLICY', 'AllowViewPolicy');

use
	\Asenine\Element\Input
	;

require '../Init.inc.php';

$pageTitle = _('System');
$pageSubtitle = _('Policies');

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
	<legend><? echo \Element\Tag::legend('key', _('Policies')); ?></legend>

	<? echo $PolicyList; ?>

	<? echo $IOCall->getHead(); ?>

	<div class="ajaxEdit">
		<input type="hidden" name="policyID">

		<fieldset>
			<legend><? echo \Element\Tag::legend('wrench_orange', _('Function')); ?></legend>
			<?
			echo \Element\Table::inputs()
				//->addRow(_('Aktiverad'), \Element\Input::checkbox('isEnabled'))
				->addRow(_('Name'), Input::text('policy')->size(40))
				->addRow(_('Description'), Input::text('description')->size(80));
			?>
		</fieldset>

		<? echo $Control; ?>
	</div>

	<? echo $IOCall->getFoot(); ?>

</fieldset>
<?
require FOOTER;
<?
define('ACCESS_POLICY', 'AllowViewSecurityBlockedIP');

require '../Init.inc.php';

$pageTitle = _('Security');
$pageSubtitle = _('IP-Block');

$HostList = \Element\Antiloop::getAsDomObject('SecurityBlockedIPs.Load');

$IOCall = new \Element\IOCall('SecurityBlockedIP');
$Control = new \Element\IOControl($IOCall);
$Control
	->addButton(new \Element\Button\Clear())
	->addButton(new \Element\Button\Save())
	->addButton(new \Element\Button\Delete());

require HEADER;
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('computer_key', _('IP-Addresses')); ?></legend>

	<? echo $HostList; ?>

	<? echo $IOCall->getHead(); ?>

	<div class="ajaxEdit">
		<input type="hidden" name="securityBlockedIPID">

		<fieldset>
			<legend><? echo \Element\Tag::legend('wrench_orange', _('Alter')); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Active'), \Element\Input::checkbox('isEnabled'))
				->addRow(_('IP-Address'), \Element\Input::text('hostAddress'))
				->addRow(_('Comment'), \Element\TextArea::small('comment'));
			?>
		</fieldset>

		<? echo $Control; ?>
	</div>

	<? echo $IOCall->getFoot(); ?>

</fieldset>
<?
require FOOTER;
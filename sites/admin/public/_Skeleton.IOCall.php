<?
define('ACCESS_POLICY', 'AllowViewSection');

require '../Init.inc.php';

$pageTitle = _('Main Title');
$pageSubtitle = _('Sub Title');

$IOCall = new \Element\IOCall('IOFile');

require HEADER;

echo $IOCall->getHead();
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('section_icon', _('Section name')); ?></legend>
	<?
	$Control = new \Element\IOControl($IOCall);
	$Control
		->addButton(\Element\Button::IO('action', 'icon', _('Caption')))
		->addButton(\Element\Button::IO('action', 'icon', _('Caption')))
		;

	echo $Control;
	?>
</fieldset>
<?
echo $IOCall->getFoot();

require FOOTER;
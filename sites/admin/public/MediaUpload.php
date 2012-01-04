<?
define('ACCESS_POLICY', 'AllowUploadMedia');

require '../Init.inc.php';

$MessageBox = new \Element\MessageBox();

if( isset($_FILES) && is_array($_FILES) && count($_FILES) > 0 )
{
	print_r($_FILES);

	foreach($_FILES['media']['name'] as $index => $fileName)
	{
		if( empty($fileName) ) continue; ### Skip empty field

		try
		{
			$fileType = $_FILES['media']['type'][$index];
			$filePath = $_FILES['media']['tmp_name'][$index];
			$fileSize = $_FILES['media']['size'][$index];
			$mediaType = $_POST['mediaType'][$index] ?: null;

			if( !is_file($filePath) )
				throw New Exception('File not found on disk. Might be too large.');

			if( !$Media = \Operation\Media::importFileToLibrary($filePath, $fileName, $mediaType) )
				throw New Exception(MESSAGE_ERROR_SYSTEM_GENERAL);

			$MessageBox->addNotice('Upload Success "' . $fileName . '": Identified as: ' . $Media::DESCRIPTION . ', Media ID: ' . sprintf('<a href="/MediaEdit.php?mediaID=%1$u">%1$u</a>', $Media->mediaID));
		}
		catch(Exception $e)
		{
			$MessageBox->addError('Upload Error "' . $fileName . '": ' . $e->getMessage());
		}
	}
}
else
{
	$MessageBox->addNotice(sprintf('Max File Size: %s', ini_get('upload_max_filesize')));
}

$Table = new \Element\Table();

while($i++ < 10)
	$Table->addRow(
		sprintf('File #%u', $i),
		\Element\Input::file("media[$i]")->size(64),
		new \Element\Module('SelectBox.MediaTypes', "mediaType[$i]", true));

$pageTitle = _('Media');
$pageSubtitle = _('Upload');

$IOCall = new \Element\IOCall('Media');

require HEADER;
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('mouse_add', 'Drag & Drop'); ?></legend>

	<form action="" method="post">
		<?
		echo \Element\Table::inputs()
			->addRow(_('Typ'), new \Element\Module('SelectBox.MediaTypes', 'preferredMediaType', true))
			;

		echo new \Element\FileUpload($IOCall);
		?>
	</form>
</fieldset>

<?
echo $IOCall->getHead();
$IOControl = new \Element\IOControl($IOCall);
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('world_link', 'Fetch Resource'); ?></legend>

	<?
	echo \Element\Table::inputs()
		->addRow(_('URL'), \Element\Input::text('url')->size(100))
		;

	echo $IOControl->setButtons(\Element\Button::IO('url', 'world_add', 'Download'));
	?>
</fieldset>
<?
echo $IOCall->getFoot();
?>

<form action="?upload=1" method="post" enctype="multipart/form-data">
<fieldset>
	<legend><? echo \Element\Tag::legend('application_form_edit', 'File List'); ?></legend>

	<?
	echo
		$MessageBox,
		$Table;

	echo \Element\Button::submit('arrow_divide', 'Upload');
	?>
</fieldset>
</form>
<?
require FOOTER;
<?
define('ACCESS_POLICY', 'AllowUploadMedia');

require '../Init.inc.php';

$MessageBox = new \Element\MessageBox();

if( isset($_FILES) && is_array($_FILES) && count($_FILES) > 0 )
{
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

#$MessageBox->addNotice(sprintf('Max File Size: %s', ini_get('upload_max_filesize')));

$pageTitle = _('Media');
$pageSubtitle = _('Upload');

$IOCall = new \Element\IOCall('Media', array('mediaID' => $_GET['mediaID']));

$UploadForm = new \Element\Form\Upload($IOCall);
$UploadForm->countBrowseFields = 5;

if( isset($_GET['mediaID']) )
{
	$pageSubtitle = sprintf('Upload Replacement for %u', $_GET['mediaID']);
	$UploadForm->showBrowseFields = false;
}


require HEADER;

echo $MessageBox;

echo $UploadForm;

require FOOTER;
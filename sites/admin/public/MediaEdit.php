<?
define('ACCESS_POLICY', 'AllowViewMedia');

require '../Init.inc.php';

$isAdmin = $User->isAdministrator();
$displayFullPaths = $isAdmin;


if( !$Media = \Manager\Media::loadOneFromDB($_GET['mediaID']) )
{
	if( !$mediaHash = \Manager\Dataset\Media::getHashFromID($_GET['mediaID']) )
		\Element\Page::error("Media could not be found");
	else
		header("Location: /MediaRepair.php?mediaID=" . $_GET['mediaID']) && exit();
}

$filePath = $Media->getFilePath();
$fileExists = file_exists($filePath);
$fileReadable = $fileExists && is_readable($filePath);
$fileWritable = $fileExists && is_writable($filePath);
$fileSize = $fileReadable ? filesize($filePath) : null;

$mediaInfo = \Manager\Dataset\Media::getData($Media->mediaID);

if( isset($_GET['download']) && $fileReadable )
{
	sendFile($filePath, $mediaInfo['fileOriginalName'] ?: sprintf('Media_%u.unknownExt', $Media->mediaID));
	exit();
}

$MediaInfo = \Element\Table::inputs();
$MediaInfo
	->addRow(_('Type'), new \Element\Module('SelectBox.MediaTypes', 'mediaType', true, $Media::TYPE))
	->addRow(_('ID'), $Media->mediaID . ' ' . sprintf('(<a href="/MediaUpload.php?mediaID=%u">%s</a>)', $Media->mediaID, _('Replace')))
	->addRow(_('Hash'), $Media->mediaHash)
	->addRow(_('Upload Time'), Format::timestamp($mediaInfo['timeCreated']))
	->addRow(_('Orginal Fiilename'), $isAdmin ? \Element\Input::text('fileOriginalName', $mediaInfo['fileOriginalName'])->size(32) : $mediaInfo['fileOriginalName'] ?: MESSAGE_NOT_AVAILABLE)
	->addRow(_('Source file'), $displayFullPaths ? $filePath : str_replace(DIR_MEDIA, '', $filePath))
	->addRow(_('Exists'), sprintf('%s %s', $fileExists ? MESSAGE_POSITIVE : MESSAGE_NEGATIVE, $fileReadable ? sprintf('(<a href="?mediaID=%u&download=1">%s</a>)', $Media->mediaID, _('Download')) : ''))
	->addRow(_('Readable'), $fileReadable ? MESSAGE_POSITIVE : MESSAGE_NEGATIVE)
	->addRow(_('Writeable'), $fileWritable ? MESSAGE_POSITIVE : MESSAGE_NEGATIVE)
	->addRow(_('Filesize'), $fileSize ? \Format::fileSize($fileSize) : MESSAGE_NOT_AVAILABLE);


$IOCall = new \Element\IOCall('Media', array('mediaID' => $Media->mediaID));

$MediaControl = new \Element\IOControl($IOCall);
$MediaControl
	->addButton(new \Element\Button\Save())
	->addButton(new \Element\Button\Delete())
	->addButton(\Element\Button::IO('publishToImgur', 'world', _('Publish')))
	;

$AutogenControl = new \Element\IOControl($IOCall);
$AutogenControl
	->createButton('flushAutogen', 'bin', _('Töm'), _('Är du säker på att du vill radera all automatgenererad media?'));


$pageTitle = _('Media');
$pageSubtitle = '#'.$Media->mediaID;

require HEADER;

echo $IOCall->getHead();
?>
<fieldset>
	<legend><? echo _('Information'); ?></legend>

	<?
	echo \Element\Input::hidden('mediaID', $Media->mediaID);
	echo
		$MediaInfo,
		$MediaControl;
	?>

</fieldset>
<?
echo $IOCall->getFoot();
?>
<fieldset>
	<legend><? echo _('Förhandsgranskning'); ?></legend>
	<?
	$MediaPreview = new \Element\MediaPreview($Media);
	echo $MediaPreview;
	?>
</fieldset>
<?
require FOOTER;
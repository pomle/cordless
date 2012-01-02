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
	->addRow(_('Typ'), new \Element\Module('SelectBox.MediaTypes', 'mediaType', true, $Media::TYPE))
	->addRow(_('Hash'), $Media->mediaHash)
	->addRow(_('Uppladdningsdatum'), Format::timestamp($mediaInfo['timeCreated']))
	->addRow(_('Orginalfilnamn'), $isAdmin ? \Element\Input::text('fileOriginalName', $mediaInfo['fileOriginalName'])->size(32) : $mediaInfo['fileOriginalName'] ?: MESSAGE_NOT_AVAILABLE)
	->addRow(_('Källfil'), $displayFullPaths ? $filePath : str_replace(DIR_MEDIA, '', $filePath))
	->addRow(_('Existerar'), sprintf('%s %s', $fileExists ? MESSAGE_POSITIVE : MESSAGE_NEGATIVE, $fileReadable ? sprintf('(<a href="?mediaID=%u&download=1">%s</a>)', $Media->mediaID, _('Ladda ner')) : ''))
	->addRow(_('Läsbar'), $fileReadable ? MESSAGE_POSITIVE : MESSAGE_NEGATIVE)
	->addRow(_('Skrivbar'), $fileWritable ? MESSAGE_POSITIVE : MESSAGE_NEGATIVE)
	->addRow(_('Filstorlek'), $fileSize ? \Format::fileSize($fileSize) : MESSAGE_NOT_AVAILABLE);


$IOCall = new \Element\IOCall('Media', array('mediaID' => $Media->mediaID));

$MediaControl = new \Element\IOControl($IOCall);
$MediaControl
	->addButton(new \Element\Button\Save())
	->addButton(new \Element\Button\Delete());

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
	echo
		$MediaInfo,
		$MediaControl;
	?>

</fieldset>

<fieldset>
	<legend><? echo _('Förhandsgranskning'); ?></legend>
	<?
	$MediaPreview = new \Element\MediaPreview($Media);
	echo $MediaPreview;
	?>
</fieldset>

<?
$files = \Manager\Dataset\Media::getSpreadByHash($Media->mediaHash);
?>
<fieldset>
	<legend><? printf(_('Biblioteksförekomst (%u)'), count($files)); ?></legend>
	<?
	if( count($files) > 0 )
	{
		echo '<ul>';
		foreach($files as $file)
			printf('<li><a href="%s">%s</a></li>',
				str_replace(DIR_MEDIA, URL_MEDIA, $file),
				htmlspecialchars($displayFullPaths ? $file : str_replace(DIR_MEDIA, '', $file)));
		echo '</ul>';
	}

	unset($files);

	echo $AutogenControl;
	?>
</fieldset>

<?
echo $IOCall->getFoot();

require FOOTER;


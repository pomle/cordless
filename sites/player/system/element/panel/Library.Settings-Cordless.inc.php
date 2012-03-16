<?
namespace Cordless;

use
	\Asenine\DB,
	\Asenine\Element\Input,
	\Asenine\Element\SelectBox;

echo Element\Library::head(_("Cordless Settings"));
?>
<form action="<? echo apiLink('User.Settings.Cordless'); ?>" method="POST">
	<div class="settings">

		<?
		$formats = array(
			'' => _('Auto'),
			'mp3' => _('MP3'),
			'ogg' => _('OGG')
		);

		$Format_Stream = SelectBox::keyPair('Stream_Play_Format', $User->getSetting('Stream_Play_Format'), $formats);
		$Format_Download = SelectBox::keyPair('Stream_Download_Format', $User->getSetting('Stream_Download_Format'), $formats);

		echo Element\Table::inputs()
			->addRow("Custom Background URL", Input::text('WebUI_Global_Background_URL', $User->getSetting('WebUI_Global_Background_URL'))->size(32)->addClass('url'))
			->addRow("Lock Background", Input::checkbox('WebUI_Global_Background_isLocked', $User->getSetting('WebUI_Global_Background_isLocked'), 'backgroundLocked'))
			->addRow("Stream Music As", $Format_Stream)
			->addRow("Download Music As", $Format_Download)
			;
		?>

		<button type="submit" class="formTrigger" name="action" value="save"><? echo _("Save"); ?></button>
		<div class="response"></div>
	</div>
</form>

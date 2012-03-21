<?
namespace Cordless;

use
	\Asenine\DB,
	\Asenine\Element\Input,
	\Asenine\Element\SelectBox;

echo Element\Library::head(_("Last.fm Settings"));
?>
<form action="<? echo apiLink('User.Settings.Lastfm'); ?>" method="POST">
	<div class="settings">

		<?
		echo Element\Table::inputs()
			->addRow("Scrobble", Input::checkbox('last_fm_scrobble', $User->last_fm_scrobble))
			->addRow("Love Starred Tracks", Input::checkbox('last_fm_love_starred_tracks', $User->last_fm_love_starred_tracks))
			->addRow("Unlove Unstarred Tracks", Input::checkbox('last_fm_unlove_unstarred_tracks', $User->last_fm_unlove_unstarred_tracks))
			;
		?>

		<button type="submit" class="formTrigger" name="action" value="save"><? echo _("Save"); ?></button>
		<div class="response"></div>
	</div>
</form>
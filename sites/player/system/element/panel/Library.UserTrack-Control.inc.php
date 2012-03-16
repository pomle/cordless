<?
namespace Cordless;

use
	\Asenine\DB,
	\Asenine\Element\Input;

if( !isset($_GET['userTrackID']) )
	die('No userTrackID given');

if( !$UserTrack = UserTrack::loadFromDB($_GET['userTrackID']) )
	die('Track not found');

$userTrackTitle = (string)$UserTrack;
$userTrackTitlePreviewMaxLen = 15;

echo Element\Library::head(
	$UserTrack->title,
	$UserTrack->artist,
	sprintf(_("Track (%s)"), mb_strlen($userTrackTitle) > $userTrackTitlePreviewMaxLen + 3 ? mb_substr($userTrackTitle, 0, $userTrackTitlePreviewMaxLen) . '...' : $userTrackTitle)
);



?>
<div class="edit">
	<form action="/api/?method=UserTrack.Edit" method="POST">
		<div class="settings">

			<?
			$size = 32;

			echo Input::hidden('userTrackID', $UserTrack->userTrackID);

			echo Element\Table::inputs()
				->addRow("Artist",  Input::text('artist', $UserTrack->artist)->size($size))
				->addRow("Title",  Input::text('title', $UserTrack->title)->size($size))
				#->addRow("Album", Element\Input::text('album', $UserTrack->album)->size($size))
				#->addRow("Year", Element\Input::text('year', $UserTrack->year)->size($size))
				#->addRow("Starred",  Input::checkbox('isStarred', $UserTrack->isStarred))
				->addRow("Filename", Input::text('filename', $UserTrack->filename)->size($size))
				;
			?>

			<button type="submit" name="action" value="update" class="formTrigger"><? echo _("Save"); ?></button>
			<button type="submit" name="action" value="delete" class="formTrigger"><? echo _("Delete"); ?></button>

			<div class="response"></div>
		</div>
	</form>
<div>
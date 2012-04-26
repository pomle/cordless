<?
namespace Cordless;

use
	\Asenine\DB,
	\Asenine\Element\Input;

global $params;

if( !isset($params->userTrackID) )
	throw new PanelException('No userTrackID given');

if( !$UserTrack = UserTrack::loadFromDB($params->userTrackID) )
	throw new PanelException('Track not found');

if( !$UserTrack->isAccessible($User) )
	throw new PanelException('Track not accessible');


$userTrackTitle = (string)$UserTrack;
$userTrackTitlePreviewMaxLen = 15;

echo Element\Library::head(
	null,
	null,
	sprintf(_("Track (%s)"), mb_strlen($userTrackTitle) > $userTrackTitlePreviewMaxLen + 3 ? mb_substr($userTrackTitle, 0, $userTrackTitlePreviewMaxLen) . '...' : $userTrackTitle)
);

$imageURL = null;
if( isset($UserTrack->Image) )
	$imageURL = getUserTrackImageURL($UserTrack->Image->mediaHash);

?>
<form action="<? echo apiLink('UserTrack.Edit'); ?>" method="POST">
	<div class="userTrackEdit">
		<?
		echo Input::hidden('userTrackID', $UserTrack->userTrackID);
		?>

		<div class="canvas">
			<div class="image" style="background-image: url('<? echo htmlspecialchars($imageURL); ?>');">
				<a href="#" class="userTrackPlay" data-usertrackid="<? echo $UserTrack->userTrackID; ?>"></a>
			</div>
		</div>

		<ul class="meta">
			<li class="artist">
				<? echo Input::text('artist', $UserTrack->artist)->addClass('artist')->size(40); ?>
			</li>
			<li class="title">
				<? echo Input::text('title', $UserTrack->title)->addClass('artist')->size(40); ?>
			</li>
		</ul>

		<ul>
			<li><a href="#" class="userTrackPlay" data-usertrackid="<? printf('%d', $UserTrack->userTrackID); ?>"><? echo _("Play Now"); ?></a></li>
			<li><a href="<? echo URL_PLAYER, sprintf('?userTrackID=%d', $UserTrack->userTrackID); ?>"><? echo _("Direct Link"); ?></a></li>
		</ul>

		<button type="submit" name="action" value="update" class="formTrigger"><? echo _("Save"); ?></button>
		<button type="submit" name="action" value="delete" class="formTrigger"><? echo _("Delete"); ?></button>

		<div class="response"></div>
	</div>
</form>
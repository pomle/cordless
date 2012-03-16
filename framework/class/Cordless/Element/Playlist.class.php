<?
namespace Cordless\Element;

class Playlist extends UserTrackList
{
	public function __toString()
	{
		ob_start();
		?>
		<div class="playlist">

			<div class="control">
				<a href="#" class="item clear" title="<? echo htmlspecialchars(_("Clear Queue")); ?>"><? echo _('Clear'); ?></a>
				<a href="#" class="item save" title="<? echo htmlspecialchars(_("Save Queue as Playlist")); ?>"><? echo _('Save'); ?></a>
				<a href="#" class="item shuffle" title="<? echo htmlspecialchars(_("Shuffle Queue")); ?>"><? echo _('Shuffle'); ?></a>
			</div>

			<div class="userTracks">
				<?
				echo $this->getItemsHTML();
				?>
			</div>

		</div>
		<?
		return ob_get_clean();
	}
}
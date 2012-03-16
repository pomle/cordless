<?
namespace Cordless\Element;

class Tracklist extends UserTrackList
{
	public function __toString()
	{
		ob_start();
		?>
		<div class="tracklist">

			<a class="toggleView" title="<? echo _("Toggle view between tile and list mode"); ?>"><? echo _("Toggle Track View"); ?></a>

			<div class="control">
				<a href="#" class="item queueLast" title="<? echo htmlspecialchars(_("Append tracks to play queue")); ?>"><? echo htmlspecialchars(_('Queue Last')); ?></a>
				<a href="#" class="item queueNext" title="<? echo htmlspecialchars(_("Add tracks after currently playing track")); ?>"><? echo htmlspecialchars(_('Queue Next')); ?></a>
				<a href="#" class="item queueReplace" title="<? echo htmlspecialchars(_("Replace play queue with tracks")); ?>"><? echo htmlspecialchars(_('Replace Queue and Play')); ?></a>
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
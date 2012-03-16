<?
namespace Cordless\Element;

class Player
{
	public function __construct()
	{
		$this->playlist = array();
	}

	public function __toString()
	{
		ob_start();
		?>
		<section id="player">

			<div class="time">
				<span class="current">0:00</span> /
				<span class="total">0:00</span>
			</div>

			<div class="control playback">
				<a href="#" class="item play_pause" title="<? echo htmlspecialchars(_('Toggle Play/Pause (Spacebar)')); ?>"><? echo htmlspecialchars(_('Play/Pause')); ?></a>
				<a href="#" class="item prev" title="<? echo htmlspecialchars(_('Prev Queue Track (Left)')); ?>"><? echo htmlspecialchars(_('Prev')); ?></a>
				<a href="#" class="item next" title="<? echo htmlspecialchars(_('Next Queue Track (Right)')); ?>"><? echo htmlspecialchars(_('Next')); ?></a>
			</div>

			<a href="#" class="scrubber">
				<div class="scrubArea">
					<div class="progress"></div>
				</div>
			</a>

			<ul class="trackinfo">
				<li class="item"><a href="#NowPlaying" class="panelLibrary state"></a></li>
				<li class="item playingUserTrack"></li>
				<li class="item artist"></li>
				<li class="item title"></li>
				<li class="item error"></li>
			</ul>

		</section>
		<?
		return ob_get_clean();
	}

	public function addUserTrackItem(UserTrackItem $UserTrackItem)
	{
		$this->playlist[] = $UserTrackItem;
		return $this;
	}

	public function addUserTrackItems(Array $userTrackItems)
	{
		foreach($userTrackItems as $UserTrackItem)
			$this->addUserTrack($UserTrackItem);

		return $this;
	}

	public function getPlaylist()
	{
		ob_start();
		?>
		<div class="items">
			<?
			foreach($this->playlist as $UserTrackItem)
				echo $UserTrackItem;
			?>
		</div>
		<?
		return ob_get_clean();
	}
}
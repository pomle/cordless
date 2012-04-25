<?
namespace Cordless\Element;

class UserTrackItem
{
	public
		$userTrackID,
		$title,
		$artist,
		$imageURL,
		$isOwner = false,
		$isStarred = false;

	public static function fromUserTrack(\Cordless\UserTrack $UserTrack)
	{
		$UTI = new self($UserTrack->userTrackID, $UserTrack->title, $UserTrack->artist);

		if( isset($UserTrack->Track->artists[0]) )
		{
			$Artist = $UserTrack->Track->artists[0];
			$UTI->artistID = $Artist->artistID;
		}

		$UTI->isOwner = $UserTrack->isOwner;
		$UTI->isAccessible = $UserTrack->isAccessible;
		$UTI->isStarred = $UserTrack->isStarred;
		$UTI->duration = $UserTrack->Track->duration;

		if( isset($UserTrack->Image) )
			$UTI->imageURL = \Cordless\getUserTrackItemImageURL($UserTrack->Image->mediaHash);

		return $UTI;
	}

	public function __construct($userTrackID, $title, $artist, $imageURL = null)
	{
		$this->userTrackID = $userTrackID;
		$this->title = $title;
		$this->artist = $artist;

		$this->imageURL = $imageURL;
	}

	public function __toString()
	{
		ob_start();
		?>
		<div class="userTrack <?
			printf(' id%d', $this->userTrackID);
			if( $this->isOwner ) echo " isOwner";
			if( $this->isAccessible ) echo " isAccessible";
			if( $this->isStarred ) echo " isStarred";
			?>"
			data-usertrackid="<? echo $this->userTrackID; ?>"
			data-artist="<? echo htmlspecialchars($this->artist); ?>"
			data-title="<? echo htmlspecialchars($this->title); ?>"
			>

			<div class="image">
				<? if( $this->imageURL ) printf('<img src="%s">', $this->imageURL); ?>
			</div>

			<div class="control track">
				<a href="#" class="item starToggle" title="<? echo htmlspecialchars(_("Star/Unstar track")); ?>"><? echo htmlspecialchars(_('Star/Unstar')); ?></a>
				<a href="#" class="item takeOwnership" title="<? echo htmlspecialchars(_("Claim track")); ?>"><? echo htmlspecialchars(_('Claim')); ?></a>
			</div>

			<div class="control playqueue">
				<a href="#" class="item queueLast" title="<? echo htmlspecialchars(_("Append track to play queue")); ?>"><? echo htmlspecialchars(_('Queue Last')); ?></a>
				<a href="#" class="item queueNext" title="<? echo htmlspecialchars(_("Add track after currently playing track")); ?>"><? echo htmlspecialchars(_('Queue Next')); ?></a>
				<a href="#" class="item queuePlay" title="<? echo htmlspecialchars(_("Add track after currently playing track and start playing")); ?>"><? echo htmlspecialchars(_('Queue Next and Play')); ?></a>
				<a href="#" class="item removeItem" title="<? echo htmlspecialchars(_("Remove track")); ?>"><? echo htmlspecialchars(_('Remove')); ?></a>
			</div>

			<ul class="meta">
				<li>
					<a class="item title"><? echo htmlspecialchars($this->title); ?></a>
				</li>
				<li>
					<a class="item artist"><? echo htmlspecialchars($this->artist); ?></a>
				</li>
				<li>
					<span class="item duration"><? echo isset($this->duration) ? \Cordless\formatDuration($this->duration) : ''; ?></span>
				</li>
			</ul>

		</div>
		<?
		return ob_get_clean();
	}
}
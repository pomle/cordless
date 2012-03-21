<?
namespace Cordless\Element;

interface iUserTrackList
{
	public function __toString();
}

abstract class UserTrackList
{
	protected
		$Fetcher;

	public static function createFromFetcher(\Cordless\Fetch\UserTrack $Fetcher)
	{
		$UserTrackList = self::createFromUserTracks( $Fetcher() );
		$UserTrackList->Fetcher = $Fetcher;
		return $UserTrackList;
	}

	public static function createFromUserTracks(Array $userTracks)
	{
		$I = new static();
		$i = 0;
		foreach($userTracks as $UserTrack)
		{
			$UserTrackItem = UserTrackItem::fromUserTrack($UserTrack);
			$I->addUserTrackItem($UserTrackItem);
		}
		return $I;
	}

	public static function createFromUserTrackItems(Array $userTrackItems)
	{
		$I = new static();
		$I->addUserTrackItems($userTrackItems);
		return $I;
	}

	public function __construct()
	{
		$this->length = 0;
		$this->userTrackItems = array();
	}


	public function addUserTrack(\Music\UserTrack $UserTrack)
	{
		$UserTrackItem = UserTrackItem::fromUserTrack($UserTrack);
		$this->addUserTrackItem($UserTrackItem);
		return $this;
	}

	public function addUserTracks(Array $userTracks)
	{
		foreach($userTracks as $UserTrack)
			$this->addUserTrack($UserTrack);

		return $this;
	}

	public function addUserTrackItem(UserTrackItem $UserTrackItem)
	{
		$this->userTrackItems[] = $UserTrackItem;
		$this->length++;
		return $this;
	}

	public function addUserTrackItems(Array $userTrackItems)
	{
		foreach($userTrackItems as $UserTrackItem)
			$this->addUserTrackItem($UserTrackItem);

		return $this;
	}

	public function getItemsHTML()
	{
		ob_start();

		foreach($this->userTrackItems as $UserTrackItem)
			echo $UserTrackItem;

		if( $this->Fetcher && $this->Fetcher->hasMore )
			printf('<a class="fetchMore" data-fetcher="%s">More</a>', htmlspecialchars(json_encode($this->Fetcher)));

		return ob_get_clean();
	}
}
<?
namespace Cordless\UserTrack;

class APIObject
{
	public
		$userTrackID,
		$title,
		$artist,
		$playcount;

	public static function convArray($userTracks)
	{
		$userTracks = array_values($userTracks);

		foreach($userTracks as &$UserTrack)
			$UserTrack = new self($UserTrack);

		return $userTracks;
	}

	public function __construct(\Cordless\UserTrack $UserTrack)
	{
		$this->userTrackID = $UserTrack->userTrackID;
		$this->title = $UserTrack->title;
		$this->artist = $UserTrack->artist;
		$this->playcount = $UserTrack->playcount;
	}
}
<?
namespace Cordless\Event;

class UserTrack
{
	public static function importFile(\Cordless\User $User, \Asenine\File $File)
	{
		if( !\Asenine\Media\Type\Audio::canHandleFile($File) )
			throw New \Exception(_("Unsupported File =/"));

		$Audio = \Asenine\Media\Type\Audio::createFromFile($File);

		$Audio = \Asenine\Media::integrateIntoLibrary($Audio, $File->name);

		$Track = Track::createFromAudio($Audio);

		### Check to see if track already in users library
		$query = \Asenine\DB::prepareQuery("SELECT ID FROM Cordless_UserTracks WHERE userID = %u AND trackID = %u", $User->userID, $Track->trackID);
		$userTrackID = (int)\Asenine\DB::queryAndFetchOne($query);

		if( !$userTrackID || !$UserTrack = \Cordless\UserTrack::loadFromDB($userTrackID) )
		{
			$UserTrack = new \Cordless\UserTrack($User->userID);
			$UserTrack->setTrack($Track);

			$ID3 = new \Cordless\ID3($Audio->getFilePath());

			$UserTrack->artist = $ID3->getArtist();
			$UserTrack->title = $ID3->getTitle();

			$UserTrack->filename = $File->name;

			\Cordless\UserTrack::saveToDB($UserTrack);
		}

		return $UserTrack;
	}
}
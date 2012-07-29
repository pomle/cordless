<?
namespace Cordless\Event;

class UserTrack
{
	public static function importFile(\Cordless\User $User, \Asenine\File $File, $artist = null, $title = null)
	{
		if( !\Asenine\Media\Type\Audio::canHandleFile($File) )
			throw new \Exception(_("Unsupported File =/"));

		if( !$Audio = \Asenine\Media\Type\Audio::createFromFile($File) )
			throw new \Exception(_("Failed to create Audio object"));

		if( !$Audio = \Asenine\Media::integrateIntoLibrary($Audio, $File->name) )
			throw new \Exception(_("Failed to import file to library"));

		$Track = Track::createFromAudio($Audio);


		if($artist)
			$Track->addArtist(Artist::createFromName($artist));

		if($title)
			$Track->title = $title;


		if( strlen($Track->title) == 0 )
			throw New \Exception(_('Missing track title'));

		if( strlen($Track->getArtistName()) == 0 )
			throw New \Exception(_('Missing artist name'));


		### Check to see if track already in users library
		$query = \Asenine\DB::prepareQuery("SELECT ID FROM Cordless_UserTracks WHERE userID = %u AND trackID = %u", $User->userID, $Track->trackID);
		$userTrackID = (int)\Asenine\DB::queryAndFetchOne($query);

		if( !$userTrackID || !$UserTrack = \Cordless\UserTrack::loadFromDB($userTrackID) )
		{
			$UserTrack = new \Cordless\UserTrack($User->userID);
			$UserTrack->setTrack($Track);

			$ID3 = new \Cordless\ID3($Audio->getFilePath());

			$UserTrack->artist = $artist ?: $ID3->getArtist();
			$UserTrack->title = $title ?: $ID3->getTitle();

			$UserTrack->filename = $File->name;

			\Cordless\UserTrack::saveToDB($UserTrack);
		}

		return $UserTrack;
	}
}
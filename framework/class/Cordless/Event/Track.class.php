<?
namespace Cordless\Event;

class Track
{
	public static function createFromAudio(\Asenine\Media\Type\Audio $Audio)
	{
		$filePath = $Audio->getFilePath();

		$ID3 = new \Cordless\ID3($filePath);

		$id3['artist'] = $ID3->getArtist();
		$id3['title'] = $ID3->getTitle();
		$id3['album'] = $ID3->getAlbum();
		$id3['year'] = $ID3->getYear();
		$id3['position'] = $ID3->getTrackNumber();

		return self::createManual($Audio, $id3['artist'], $id3['title'], $id3['album'], $id3['year'], $id3['position']);
	}

	public static function createManual(\Asenine\Media\Type\Audio $Audio, $artist, $title, $album = null, $year = null, $trackNo = null)
	{
		$Artist = Artist::createFromName($artist);

		$Track = new \Cordless\Track($title);

		$Track->setAudio($Audio);
		$Track->addArtist($Artist);


		if( $Track_Existing = \Cordless\Track::loadByTrack($Track) )
		{
			$Track = $Track_Existing;
		}
		else
		{
			if( $audioInfo = $Audio->getInfo() )
				$Track->duration = (int)$audioInfo['duration'];

			$Track->timeReleased = $year ? mktime(0, 0, 0, 1, 1, (int)$year) : null;
		}

		$Track->trackNo = (int)$trackNo;

		### If album is defined, add Track to album
		if( $album && ($Album = Album::createFromInfo($album, $year)) )
		{
			while(true)
			{
				### If Track already on album, just silently stop doing stuff
				foreach($Album->tracks as $AlbumTrack)
					if( $AlbumTrack->trackID == $Track->trackID ) break 2;

				$Album->addTrack($Track);

				#\Cordless\Album::saveToDB($Album);

				break;
			}
		}

		return $Track;
	}
}
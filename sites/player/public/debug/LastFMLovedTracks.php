<?
require '_Debug.php';

/*$url = sprintf('http://ws.audioscrobbler.com/2.0/?method=artist.getimages&artist=%s&api_key=%s', urlencode('Teddybears'), urlencode(LAST_FM_API_KEY));

echo $url, "\n";

$imageXML = file_get_contents($url);
$ImageXML = new SimpleXMLElement($imageXML);

foreach($ImageXML->xpath('/lfm/images/image/sizes/size[@name="original"]') as $image)
{
	echo $image, "\n";
}

die();*/

try
{
	$count = 250;

	$url = sprintf('http://ws.audioscrobbler.com/2.0/?method=user.getlovedtracks&user=%s&api_key=%s&limit=%u', urlencode('pomle'), urlencode(LAST_FM_API_KEY), $count);
	echo $url;

	$loveXML = file_get_contents($url);

	$LoveXML = new SimpleXMLElement($loveXML);

	foreach($LoveXML->xpath('/lfm/lovedtracks/track') as $track)
	{
		$lovedUTS = (int)$track->date['uts'];
		$lastFmID = $lovedUTS;

		$query = \DB::prepareQuery("SELECT postID FROM PostTracks WHERE lastFmID = %u", $lastFmID);
		$postID = \DB::queryAndFetchOne($query);

		$Post = $postID ? \Post\Track::loadOneFromDB($postID) : \Post\Track::addToDB();

		if( !isset($Post->isPublished) ) $Post->isPublished = true;

		$Post->timePublished = $lovedUTS;

		$Post->artist = (string)$track->artist->name;
		$Post->track = (string)$track->name;

		$Post->artistURL = (string)$track->artist->url;
		$Post->trackURL = (string)$track->url;

		$Post->title = sprintf('%s - %s', $Post->artist, $Post->track);

		$Post->lastFmID = $lastFmID;

		$imageURL = null;
		foreach($track->xpath('image[@size="extralarge"]') as $image)
		{
			$imageURL = (string)$image;
			if( $Media = \Operation\Media::downloadFileToLibrary($imageURL, MEDIA_TYPE_IMAGE) )
				$Post->setPreviewMedia($Media);
		}

		if( !$imageURL ) ### If no default ImageURL was supplied, fetch by artist name
		{
			$url = sprintf('http://ws.audioscrobbler.com/2.0/?method=artist.getimages&artist=%s&api_key=%s', urlencode($Post->artist), urlencode(LAST_FM_API_KEY));

			#echo $url, "\n";

			$imageXML = file_get_contents($url);
			$ImageXML = new SimpleXMLElement($imageXML);

			if( $images = $ImageXML->xpath('/lfm/images/image/sizes/size[@name="original"]') )
			{
				foreach( as $imageURL)
				{
					$imageURL = (string)$imageURL;
					if( $Media = \Operation\Media::downloadFileToLibrary($imageURL, MEDIA_TYPE_IMAGE) )
						$Post->setPreviewMedia($Media);

					break;
				}
			}
		}

		\Post\Track::saveToDB($Post);
	}
}
catch(\FileException $e)
{
	printf("Could not read \"%s\" Last FM Service probably stopped responding\n", $imageURL);
}
catch(\Exception $e)
{
	print_r($e);
	die("Error: " . $e->getMessage());
}
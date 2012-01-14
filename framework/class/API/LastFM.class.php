<?
namespace API;

class LastFMException extends \Exception
{}

class LastFM
{
	public function __construct($api_key = null, $api_secret = null)
	{
		$this->api_key = $api_key;
		$this->api_secret = $api_secret;

		$this->api_url = sprintf('http://ws.audioscrobbler.com/2.0/?api_key=%s', $this->api_key);
	}

	public function getArtistURL($artist)
	{
		if( $XML = $this->getXML('artist.getinfo', array('artist' => $artist)) )
			return (string)reset($XML->xpath('/lfm/artist/url'));

		return false;
	}

	public function getArtistImageURLs($artist)
	{
		$imageURLs = array();

		if( $XML = $this->getXML('artist.getimages', array('artist' => $artist)) )
			if( $ImageXML = $XML->xpath('/lfm/images/image/sizes/size[@name="original"]') )
				foreach($ImageXML as $imageURL)
					$imageURLs[] = (string)$imageURL;

		return $imageURLs;
	}

	public function getArtistImage($artist)
	{
		if( $imageURLs = $this->getArtistImageURLs($artist) )
			if( $Media = \Operation\Media::downloadFileToLibrary(reset($imageURLs), MEDIA_TYPE_IMAGE) ) return $Media;

		return false;
	}

	public function getTrackURL($track, $artist = null)
	{
		if( $XML = $this->getXML('track.getinfo', array('track' => $track, 'artist' => $artist)) )
			return (string)reset($XML->xpath('/lfm/track/url'));

		return false;
	}

	public function getXML($method, Array $properties = array())
	{
		$url = $this->api_url . sprintf('&method=%s', urlencode($method));

		foreach($properties as $name => $value)
			$url .= '&' . urlencode($name) . '=' . urlencode($value);

		return @simplexml_load_file($url) ?: false;
	}
}
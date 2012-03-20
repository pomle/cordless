<?
namespace Asenine\API;

class LastFMException extends \Exception
{}

class LastFM
{
	public function __construct($api_key = null, $api_secret = null)
	{
		if( strlen($api_key) != 32 )
			throw New LastFMException('Invalid Last.fm API KEY');

		$this->api_key = $api_key;
		$this->api_secret = $api_secret;

		$this->api_url = 'http://ws.audioscrobbler.com/2.0/';

		$this->returnRawXML = false;
	}

	public function getArtistURL($artist)
	{
		if( $XML = $this->sendRequest('artist.getinfo', array('artist' => $artist)) )
			return (string)reset($XML->xpath('/lfm/artist/url'));

		return false;
	}

	public function getArtistImageURLs($artist)
	{
		$imageURLs = array();

		if( $XML = $this->sendRequest('artist.getimages', array('artist' => $artist)) )
			if( $ImageXML = $XML->xpath('/lfm/images/image/sizes/size[@name="original"]') )
				foreach($ImageXML as $imageURL)
					$imageURLs[] = (string)$imageURL;

		return $imageURLs;
	}

	public function getArtistImage($artist)
	{
		if( $imageURLs = $this->getArtistImageURLs($artist) )
			if( $Media = \Asenine\Media\Operation::downloadFileToLibrary(reset($imageURLs), ASENINE_MEDIA_TYPE_IMAGE) )
				return $Media;

		return false;
	}

	public function getSession($token) ### When a user has visited Last.fm an authorized your app, you get a token from Last.fm that you use to obtain session
	{
		$params = array('token' => $token);

		$xml = $this->sendRequest('auth.getSession', $params, true);

		if( $xml->xpath('/lfm[@status="ok"]') )
		{
			$Session = new \stdClass();
			foreach($xml->xpath('/lfm/session/*') as $item)
			{
				$key = $item->getName();
				$value = (string)$item;
				$Session->$key = $value;
			}

			return $Session;
		}

		return false;
	}

	public function getSignature(Array $params)
	{
		if( strlen($this->api_secret) != 32 )
			throw New LastFMException('Invalid Last.fm API Secret');

		ksort($params, SORT_STRING); ### Sort by param key since signature generation requires all params in order

		$string = '';

		foreach($params as $key => $value)
			$string .= (string)$key . (string)$value;

		$string .= $this->api_secret;

		return md5($string);
	}

	public function getTrackURL($track, $artist = null)
	{
		if( $XML = $this->sendRequest('track.getinfo', array('track' => $track, 'artist' => $artist)) )
			return (string)reset($XML->xpath('/lfm/track/url'));

		return false;
	}

	public function sendRequest($method, Array $params = array(), $sign = false)
	{
		$url = $this->api_url;

		$params['method'] = $method;

		$params['api_key'] = $this->api_key;

		if( $sign )
		{
			$params['api_sig'] = $this->getSignature($params);

			$body = '';
			foreach($params as $key => $value)
				$body .= urlencode($key) . '=' . urlencode($value) . '&';

			$body = rtrim($body, '&');

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER,
				array(
					"Accept: application/xml",
					"Content-Type: application/x-www-form-urlencoded",
					"Content-Length: " . strlen($body)
				)
			);

			if( !$response = curl_exec($ch) )
				throw New LastFMException(curl_error($ch));

			if( $response )
				return $this->returnRawXML ? $response : @simplexml_load_string($response);

			return false;
		}
		else
		{
			$url .= '?';
			foreach($params as $name => $value)
				$url .= urlencode($name) . '=' . urlencode($value) . '&';

			$this->last_url = $url;

			return $this->returnRawXML ? file_get_contents($url) : @simplexml_load_file($url);
		}
	}

	public function sendScrobble($session_key, $uts, $artist, $track)
	{
		$params = array
		(
			'sk' => $session_key,
			'timestamp' => (string)$uts,
			'artist' => $artist,
			'track' => $track,
			'chosenByUser' => '1'
		);

		return $this->sendRequest('track.scrobble', $params, true);
	}

	public function trackLove($session_key, $artist, $track)
	{
		$params = array
		(
			'sk' => $session_key,
			'artist' => $artist,
			'track' => $track
		);

		return $this->sendRequest('track.love', $params, true);
	}

	public function trackUnlove($session_key, $artist, $track, $duration = null)
	{
		$params = array
		(
			'sk' => $session_key,
			'artist' => $artist,
			'track' => $track,
			'duration' => is_numeric($duration) ? (int)$duration : null
		);

		return $this->sendRequest('track.unlove', $params, true);
	}

	public function updateNowPlaying($session_key, $uts, $artist, $track, $duration = null)
	{
		$params = array
		(
			'sk' => $session_key,
			'timestamp' => (string)$uts,
			'artist' => $artist,
			'track' => $track,
			'duration' => is_numeric($duration) ? (int)$duration : null
		);

		return $this->sendRequest('track.updateNowPlaying', $params, true);
	}
}
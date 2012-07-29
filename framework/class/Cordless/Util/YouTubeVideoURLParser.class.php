<?
namespace Cordless\Util;

class YouTubeVideoURLParser
{
	public
		$html = null, 
		$urls = array();

	public function __construct($url)
	{
		$this->html = $html = file_get_contents($url);

		if( preg_match('/yt.playerConfig.*(\{.+\});/U', $html, $matches) )
		{
			$jsonObj = json_decode($matches[1]);

			$streams = explode(',', $jsonObj->args->url_encoded_fmt_stream_map);

			$urls = array();

			foreach($streams as $stream)
			{
				$data = urldecode($stream);

				if( preg_match('/url=(.*);/', $data, $matches) )
				{
					$url = $matches[1];

					$value = 
						(strpos($url, 'quality=large') ? 8 : 0) +
						(strpos($url, 'quality=medium') ? 4 : 0) + 
						(strpos($url, 'type=video/mp4') ? 8 : 0) + 
						(strpos($url, 'quality=hd720') ? 4 : 0);

					$urls[$url] = $value; 
				}
			}

			arsort($urls);
		}

		$this->urls = array_keys($urls);
	}
}
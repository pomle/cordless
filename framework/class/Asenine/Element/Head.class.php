<?
namespace Asenine\Element;

class Head
{
	public
		$contentType,
		$charset,
		$title,
		$description,
		$canonical;


	public function __construct()
	{
		$this->contentType = 'text/html';
		$this->charset = 'utf-8';
		$this->meta = $this->links = array();
	}

	public function __toString()
	{
		ob_start();
		$this->display();
		return ob_get_clean();
	}


	public function addCSS()
	{
		foreach(func_get_args() as $url)
			$this->addLink('stylesheet', $url, 'text/css');

		return $this;
	}

	public function addMeta($name, $content)
	{
		$this->meta[] = array($name, $content);
		return $this;
	}

	public function addLink($rel, $href, $type = null, $title = null)
	{
		$this->links[] = array($type, $href, $rel, $title);
		return $this;
	}

	public function display()
	{
		### Removed since we should not rely on this being interpreted from the DOM. Content-type and charset should always be supplied as header
		#printf('<meta http-equiv="content-type" content="%s; charset=%s">', $this->contentType, $this->charset);

		if( $this->title )
			printf('<title>%s</title>', htmlspecialchars($this->title));

		if( $this->description )
			printf('<meta name="description" content="%s">', htmlspecialchars($this->description));

		if( count($this->meta) )
			foreach($this->meta as $meta)
				printf('<meta name="%s" content="%s">', $meta[0], htmlspecialchars($meta[1]));

		if( count($this->links) )
			foreach($this->links as $link)
				echo '<link href="', $link[1], '" rel="', $link[2], '"', ($link[0] ? ' type="' . $link[0] . '"' : ''), ($link[3] ? ' title="' . $link[3] . '"' : ''), '>';

		if( $this->canonical )
		{
			$href = $this->canonical;
			if( REGIONAL_LOCALE_ID === FINLAND ) $href = str_replace('coolstuff.fi', 'coolstuff.se', $href);
			printf('<link rel="canonical" href="%s">', $href);
		}
	}
}
<?
namespace Element;

global $css;
$css[] = URL_ADMIN . 'css/SubLinks.css';

class SubLinks
{
	public function __construct()
	{
		$this->icon = 'bullet_go';
		$this->caption = null;
		$this->links = array();
	}

	public function __toString()
	{
		$string = '';

		if( count($this->links) )
		{
			ob_start();
			?>
			<ul class="sublinks">
				<li class="sublink"><? echo Icon::custom($this->icon, $this->caption); ?></li>
				<?
				foreach($this->links as $link)
				{
					?><li class="sublink"><a href="<? echo $link[0]; ?>"><? echo Icon::custom($link[1], $link[2]); ?></a></li><?
				}
				?>
			</ul>
			<?
			$string = ob_get_clean();
		}

		return $string;
	}


	public function addLink($href, $icon, $caption)
	{
		$this->links[] = array($href, $icon, $caption);
		return $this;
	}
}

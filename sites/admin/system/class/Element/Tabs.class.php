<?
namespace Element;

global $css, $js;
$css[] = '/css/Tabs.css';
$js[] = '/js/Tabs.js';

class Tabs
{
	public function __construct()
	{
		$this->tabs = array();
	}

	public function __toString()
	{
		ob_start();
		?>
		<ul class="tabIndex">
			<?
			foreach($this->tabs as $tab)
			{
				?><li><a class="awesome medium flipshop" href="#<? echo $tab[0]; ?>"><?
					if( $tab[2] ) echo \Element\Icon::custom($tab[2], $tab[1]);
					echo htmlspecialchars($tab[1]);
				?></a></li><?
			}
			?>
		</ul>
		<?
		return ob_get_clean();
	}


	public function addTab($id, $caption, $icon = null)
	{
		$this->tabs[] = array($id, $caption, $icon);
		return $this;
	}
}
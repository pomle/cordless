<?
namespace Element;

class TextMenu extends \Element\_Common
{
	public function __construct()
	{
		$this->items = array();
	}

	public function __toString()
	{
		ob_start();
		?>
		<ul<? echo $this->getAttributes(); ?>>
			<?
			foreach($this->items as $item)
			{
				?><li><a href="<? echo htmlspecialchars($item[0]); ?>"><? echo htmlspecialchars($item[1]); ?></a></li><?
			}
			?>
		</ul>
		<?
		return ob_get_clean();
	}


	public function addItem($href, $caption)
	{
		$this->items[] = array($href, $caption);
		return $this;
	}
}
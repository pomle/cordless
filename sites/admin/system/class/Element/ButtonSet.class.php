<?
namespace Element;

class ButtonSet extends Common\Root
{
	public function __construct()
	{
		$this->addClass('buttonSet');
		$this->resetButtons();
	}

	public function __toString()
	{
		ob_start();
		?>
		<ul<? echo $this->getAttributes(); ?>>
			<?
			foreach($this->buttons as $Button)
			{
				?><li><? echo $Button; ?></li><?
			}
			?>
		</ul>
		<?
		return ob_get_clean();
	}


	public function addButton(Button $Button)
	{
		$this->buttons[] = $Button;
		return $this;
	}

	public function resetButtons()
	{
		$this->buttons = array();
		return $this;
	}

	public function setButtons()
	{
		$this->resetButtons();

		foreach(func_get_args() as $Button)
			$this->addButton($Button);

		return $this;
	}
}
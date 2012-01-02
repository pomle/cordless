<?
namespace Element;

class Page
{
	public static function error($string, $caption = null)
	{
		$Page = new self();

		$Page->caption = $caption ?: _('Fel');

		$MessageBox = new MessageBox();
		$MessageBox->addError($string);

		$Page->elements[] = $MessageBox;

		echo $Page;

		die(1);
	}


	public function __construct()
	{
		$this->elements = array();
	}

	public function __toString()
	{
		ob_start();

		require HEADER;
		?>
		<fieldset>
			<legend><? echo htmlspecialchars($this->caption); ?></legend>
			<?
			foreach($this->elements as $Element)
			{
				echo $Element;
			}
			?>
		</fieldset>
		<?
		require FOOTER;

		return ob_get_clean();
	}
}
<?
namespace Element\Antiloop\Field;

class Placeholder extends Common\Root
{
	public function __construct($name, $caption = null, $icon = null, $class = null, Array $data = array())
	{
		parent::__construct($name, $caption, $icon);

		$this->isSortable = false;

		$this->class = $class;

		$this->data = $data;

		$this->setContentHandler(
			function($value, $Field, $dataRow)
			{
				ob_start();
				?>
					<span class="placeholder content" <? foreach($Field->data as $prefix => $source) printf(' data-%s="%s"', $prefix, htmlspecialchars($dataRow[$source])); ?>>
						<? echo htmlspecialchars($value); ?>
					</span>
				<?
				return ob_get_clean();
			}
		);
	}
}
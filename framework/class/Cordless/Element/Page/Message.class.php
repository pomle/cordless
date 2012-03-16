<?
namespace Cordless\Element\Page;

class Message
{
	public static function error($title, $text)
	{
		return new self('error', $title, $text);
	}

	public static function notice($title, $text)
	{
		return new self('notice', $title, $text);
	}



	protected function __construct($type, $title, $text)
	{
		$this->type = $type;
		$this->title = $title;
		$this->text = $text;
	}

	public function __toString()
	{
		global $css, $js;

		$css[] = '/css/Page-Message.css';

		ob_start();

		$pageTitle = strip_tags($this->title);

		include DIR_ELEMENT . 'Header.Minimal.inc.php';
		?>
		<div class="message <? echo $this->type; ?>">
			<h1><? echo $this->title; ?></h1>

			<p><? echo nl2br($this->text); ?></p>
		</div>
		<?
		include DIR_ELEMENT . 'Footer.Minimal.inc.php';

		return ob_get_clean();
	}
}
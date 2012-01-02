<?
namespace Element;

global $css, $js;
$css[] = '/css/MessageBox.css';
$js[] = '/js/MessageBox.js';

class MessageBox
{
	public $types = array('error', 'alert', 'notice');


	public static function alert($msg)
	{
		$M = new self();
		$M->addAlert($msg);
		return $M;
	}

	public static function error($msg)
	{
		$M = new self();
		$M->addError($msg);
		return $M;
	}

	public static function notice($msg)
	{
		$M = new self();
		$M->addNotice($msg);
		return $M;
	}


	public function __construct()
	{
		$this->messages = array();
		foreach($this->types as $type)
		{
			$this->messages[$type] = array();
		}
	}

	public function __toString()
	{
		ob_start();
		?>
		<div class="messageBox">
			<?
			foreach($this->messages as $type => $strings)
			{
				$hasMessage = (count($strings) > 0);
				?>
				<ul class="message <? echo $type, $hasMessage ? ' hasMessage' : ''; ?>"><? echo nl2br(join("\n", $strings)); ?></ul>
				<?
			}
			?>
		</div>
		<?
		return ob_get_clean();
	}


	public function addAlert($string)
	{
		$this->messages['alert'][] = $string;
		return $this;
	}

	public function addError($string)
	{
		$this->messages['error'][] = $string;
		return $this;
	}

	public function addNotice($string)
	{
		$this->messages['notice'][] = $string;
		return $this;
	}
}
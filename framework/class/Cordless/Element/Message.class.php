<?
namespace Cordless\Element;

class Message
{
	const LEVEL_NOTICE = 1;
	const LEVEL_ALERT = 10;
	const LEVEL_ERROR = 255;

	protected static $levelClass = array
	(
		self::LEVEL_ALERT => 'alert',
		self::LEVEL_NOTICE => 'notice',
		self::LEVEL_ERROR => 'error',
	);

	public
		$level,
		$class,
		$text;

	public static function alert($text)
	{
		return new self(self::LEVEL_ALERT, $text);
	}

	public static function notice($text)
	{
		return new self(self::LEVEL_NOTICE, $text);
	}

	public static function error($text)
	{
		return new self(self::LEVEL_ERROR, $text);
	}


	protected function __construct($level, $text)
	{
		$this->level = $level;
		$this->class = self::$levelClass[$level];
		$this->text = $text;

		$this->html = (string)$this;
	}

	public function __toString()
	{
		return sprintf('<div class="message %s" data-level="%d">%s</div>', $this->class, $this->level, htmlspecialchars($this->text));
	}
}
<?
namespace Element;

class Icon extends Common\Root
{
	public static function custom($icon, $caption = '')
	{
		return new self($icon, $caption);
	}

	public static function help($caption)
	{
		return new self('help', $caption);
	}


	public function __construct($icon, $caption = '')
	{
		$this->icon = $icon;
		$this->caption = $caption;
		$this->addClass('icon');
	}

	public function __toString()
	{
		return sprintf
		(
			'<img %3$s src="/layout/%1$s.png" alt="%2$s" title="%2$s">',
			$this->icon,
			htmlspecialchars($this->caption),
			$this->getAttributes()
		);
	}
}
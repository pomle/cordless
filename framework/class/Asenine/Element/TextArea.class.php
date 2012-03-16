<?
namespace Asenine\Element;

class TextArea extends Common\Root
{
	public
		$name,
		$content;


	public static function small($name, $content = null)
	{
		return new self($name, $content, 30, 3);
	}

	public static function wide($name, $content = null)
	{
		return new self($name, $content, 50, 3);
	}


	public function __construct($name, $content = null, $cols, $rows)
	{
		$this->name = $name;
		$this->content = $content;
		$this->cols = (int)abs($cols) ?: 20;
		$this->rows = (int)abs($rows) ?: 3;
	}

	public function __toString()
	{
		return sprintf('<textarea %s name="%s" cols="%u" rows="%u">%s</textarea>', $this->getAttributes(), $this->name, $this->cols, $this->rows, htmlspecialchars($this->content));
	}
}
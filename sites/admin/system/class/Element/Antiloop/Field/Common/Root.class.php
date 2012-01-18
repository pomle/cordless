<?
namespace Element\Antiloop\Field\Common;

abstract class Root
{
	public
		$name,
		$caption,
		$icon,
		$headHandler,
		$contentHandler;


	public function __construct($name, $caption, $icon)
	{
		$this->name = $name;
		$this->caption = $caption;
		$this->icon = $icon;

		$this->isSortable = true;
		$this->isSortReversed = false;
		$this->isTextual = false;
	}

	final public function getContent(Array $dataRow)
	{
		$content = false;

		if( isset($this->contentHandler) )
		{
			$value = array_key_exists($this->name, $dataRow) ? $dataRow[$this->name] : null;
			$content = call_user_func_array($this->contentHandler, array($value, $this, $dataRow));
		}

		return $content;
	}

	final public function getHead()
	{
		if( isset($this->headHandler) )
		{
			return call_user_func_array($this->headHandler, array($this));
		}
		return false;
	}

	final public function setContentHandler($Function)
	{
		if( is_callable($Function) )
		{
			$this->contentHandler = $Function;
		}
		return $this;
	}

	final public function setHeadHandler($Function)
	{
		if( is_callable($Function) )
		{
			$this->headHandler = $Function;
		}
		return $this;
	}

	final public function setSortable($bool)
	{
		$this->isSortable = (bool)$bool;
		return $this;
	}

	final public function setSortReversed($bool)
	{
		$this->isSortReversed = (bool)$bool;
		return $this;
	}
}
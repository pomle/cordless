<?
namespace Element;

class Input extends Common\Root
{
	public
		$type,
		$name,
		$value;


	public static function checkbox($name, $isChecked = false, $value = '1')
	{
		$Input = new self('checkbox', $name, $value);
		$Input->isChecked = (bool)$isChecked;
		return $Input;
	}

	public static function file($name, $value = '')
	{
		return new self('file', $name, $value);
	}

	public static function hidden($name, $value = '')
	{
		return new self('hidden', $name, $value);
	}

	public static function password($name, $value = '')
	{
		return new self('password', $name, $value);
	}

	public static function radio($name, $isChecked = false, $value = '1')
	{
		$Input = new self('radio', $name, $value);
		$Input->isChecked = (bool)$isChecked;
		return $Input;
	}

	public static function text($name, $value = '')
	{
		return new self('text', $name, $value);
	}

	public static function time($name, $value = null)
	{
		return new self('text', $name, $value);
	}

	public function __construct($type, $name, $value = '')
	{
		$this->type = $type;
		$this->name = $name;
		$this->value = $value;
	}

	public function __toString()
	{
		return sprintf(
			'<input type="%s" name="%s" value="%s" %s %s %s %s>',
			$this->type,
			htmlspecialchars($this->name),
			htmlspecialchars($this->value),
			isset($this->size) ? sprintf('size="%u"', $this->size) : '',
			isset($this->maxlen) ? sprintf('maxlength="%u"', $this->maxlen) : '',
			(isset($this->isChecked) && $this->isChecked === true) ? 'checked="checked"' : '',
			$this->getAttributes()
		);
	}


	public function size($int)
	{
		$this->size = (int)$int;
		return $this;
	}

	public function maxlen($int)
	{
		$this->maxlen = (int)$int;
		return $this;
	}
}
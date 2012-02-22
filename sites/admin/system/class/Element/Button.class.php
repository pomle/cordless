<?
namespace Element;

class Button extends Common\Root
{
	public static function IO($action, $icon, $caption, $message = null)
	{
		$B = new self(null, $icon, $caption);
		$B->action = $action;
		$B->message = $message;
		return $B;
	}

	public static function submit($icon, $caption, $value = null)
	{
		$B = new self(null, $icon, $caption);
		$B->type = 'submit';
		$B->value = $value;
		return $B;
	}

	public function __construct($href = null, $icon = null, $caption = null)
	{
		$this->type = null;
		$this->href = (string)$href;
		$this->icon = (string)$icon;
		$this->caption = (string)$caption;
		$this->message = null;
		$this->attributes['class'] = array('button', 'awesome', 'medium', 'flipshop');
	}

	public function __toString()
	{
		switch($this->type)
		{
			case 'submit':
				return sprintf
				(
					'<button type="%s" %s value="%s">%s%s</button>',
					'submit',
					$this->getAttributes(),
					htmlspecialchars($this->value),
					\Element\Icon::custom($this->icon, $this->caption),
					htmlspecialchars($this->caption)
				);
			break;

			default:
				return sprintf
				(
					'<a href="%s" %s rel="%s"><img class="icon running" src="/layout/ajax_dot_loader.gif">%s%s</a>',
					htmlspecialchars($this->href),
					$this->getAttributes(),
					htmlspecialchars($this->message),
					\Element\Icon::custom($this->icon, $this->caption),
					htmlspecialchars($this->caption)
				);
			break;
		}
	}
}
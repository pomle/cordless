<?
namespace Element;

class Tool extends Common\Root
{
	public static function ajaxTrigger($icon, $caption = null, $message = null)
	{
		$Button = new static($icon, $caption);
		$Button->addClass('ajaxTrigger')->setMessage($message);
		return $Button;
	}

	public static function formTrigger($action, $icon, $caption, $message = null)
	{
		$Button = new static($icon, $caption);
		$Button->action = $action;
		$Button->setMessage($message);
		$Button->addClass('formTrigger');
		$Button->isCaptionDisplayed = true;
		return $Button;
	}

	public static function popupTrigger($protocol, $icon, $caption)
	{
		$Button = new static($icon, $caption, '/ajax/AjaxPopup.php?protocol=' . $protocol);
		$Button->addClass('popupTrigger');
		return $Button;
	}

	public static function textInsert($icon, $caption, $type)
	{
		$Button = new static($icon, $caption, $type);
		$Button->addClass('textInsert')->setHref('/ajax/AjaxRequest.php?protocol=TextEditor&amp;type=' . $type);
		return $Button;
	}


	public function __construct($icon, $caption, $href = null) {
		$this->caption = $caption;
		$this->icon = $icon;
		$this->href = $href;
	}

	public function __toString()
	{
		$html.= '<a href="' . $this->getHref() . '"';

		$html.= $this->getAttributes();

		if( strlen($this->message) > 0 ) $html.= ' rel="' . htmlspecialchars($this->message) . '"';
		$html.= '>';

		if( strlen($this->icon) > 0 ) $html.= Icon::custom($this->icon, $this->caption);
		if( $this->isCaptionDisplayed ) $html.= htmlspecialchars($this->caption);
		$html.= '</a>';

		return $html;
	}


	public function setMessage($message) {
		$this->message = $message;
		return $this;
	}

	public function setHref($href) {
		$this->href = $href;
		return $this;
	}

	public function getHref() {
		$href = $this->href;
		return $href;
	}
}
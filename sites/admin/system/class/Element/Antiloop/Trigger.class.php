<?
namespace Element\Antiloop;

class Trigger
{
	public function __construct($icon, $caption, $protocol, $action = null, array $params = array())
	{
		$this->icon = $icon;
		$this->caption = $caption;
		$this->AjaxCall = new \AjaxCall($protocol, $params, URL_IO_FETCHER);

		if( $action )
			$this->AjaxCall->addParam('action', $action);

		foreach($params as $key => $value)
		{
			$this->AjaxCall->addParam($key, $value);
		}
	}

	public function __toString()
	{
		return sprintf
		(
			'<a href="%s" class="trigger pD">%s</a>',
			htmlspecialchars($this->AjaxCall),
			\Element\Icon::custom($this->icon, $this->caption)
		);
	}
}
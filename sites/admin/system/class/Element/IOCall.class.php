<?
namespace Element;

global $js;
$js[] = URL_ADMIN . 'js/objects/AjaxEvent.js';
$js[] = URL_ADMIN . 'js/IOCall.js';

class IOCall
{
	public function __construct($protocol, array $params = array())
	{
		$this->AjaxCall = new \AjaxCall($protocol, $params, URL_IO_FETCHER);
	}

	public function getHead()
	{
		return sprintf('<form action="%s" method="post" class="IOCall" autocomplete="off">', $this->AjaxCall);
	}

	public function getFoot()
	{
		return '</form>';
	}

	public function setParam($key, $value = null)
	{
		$this->AjaxCall->setParam($key, $value);
		return $this;
	}
}
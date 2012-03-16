<?
namespace Asenine;

class AjaxCall
{
	public $url;
	public $params = array();

	public function __construct($protocol, $params = array(), $url = URL_AJAX_REQUEST)
	{
		$this->url = $url;
		$this->addParam('protocol', $protocol);

		foreach($params as $name => $value)
		{
			$this->addParam($name, $value);
		}
	}

	public function __toString()
	{
		$url = $this->url;
		if( count($this->params) > 0 )
		{
			$url.= '?';
			foreach($this->params as $name => $value)
			{
				$url .= urlencode($name) . '=' . urlencode($value) . '&';
			}
		}
		return $url;
	}


	public function addParam($name, $value)
	{
		return $this->setParam($name, $value);
	}

	public function setParam($name, $value)
	{
		$name = (string)$name;

		if( is_null($value) && isset($this->params[$name]) )
		{
			unset($this->params[$name]);
		}
		else
		{
			$this->params[$name] = (string)$value;
		}

		return $this;
	}
}

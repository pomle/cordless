<?
namespace Element;

class IOControl extends \Element\_Common
{
	public static function makeOf($IOCall)
	{
		$args = func_get_args();
		$C = new self(array_shift($args));
		foreach($args as $button)
		{
			$classPath = '\\Element\\Button\\' . ucfirst($button);
			$C->addButton(new $classPath());
		}
		return $C;
	}

	public function __construct(\Element\IOCall $IOCall)
	{
		$this->IOCall = $IOCall;
		$this->ButtonSet = new \Element\ButtonSet();
		$this->ButtonSet->addClass('IOControl');
		$this->MessageBox = new MessageBox();
	}

	public function __toString()
	{
		return
			(string)$this->ButtonSet .
			(string)$this->MessageBox;

	}


	public function addButton(\Element\Button $Button)
	{
		$AjaxCall = clone $this->IOCall->AjaxCall;
		$AjaxCall->setParam('action', $Button->action);
		$Button->href = (string)$AjaxCall;
		$this->ButtonSet->addButton($Button);
		return $this;
	}

	public function createButton($action, $icon, $caption, $message = null)
	{
		$Button = new \Element\Button(null, $icon, $caption);
		$Button->message = $message;
		$Button->action = $action;
		return $this->addButton($Button);
	}

	public function resetButtons()
	{
		$this->ButtonSet->resetButtons();
		return $this;
	}

	public function setButtons()
	{
		$this->resetButtons();

		foreach(func_get_args() as $Button)
			$this->addButton($Button);

		return $this;
	}
}
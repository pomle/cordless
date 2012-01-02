<?
namespace Element\Button;

class Save extends \Element\Button
{
	public function __construct($action = 'save', $caption = null, $icon = null, $message = null)
	{
		parent::__construct(null, $icon ?: 'disk', $caption ?: _('Spara'));
		$this->action = $action;
		$this->message = $message;
	}
}
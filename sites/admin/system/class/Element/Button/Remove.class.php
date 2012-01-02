<?
namespace Element\Button;

class Remove extends \Element\Button
{
	public function __construct($action = 'remove', $caption = null, $icon = null, $message = null)
	{
		parent::__construct(null, $icon ?: 'cancel', $caption ?: _('Ta bort'));
		$this->action = $action;
		$this->message = $message;
	}
}
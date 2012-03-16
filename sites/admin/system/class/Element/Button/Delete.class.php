<?
namespace Element\Button;

class Delete extends \Element\Button
{
	public function __construct($action = 'delete', $caption = null, $icon = null, $message = null)
	{
		parent::__construct(null, $icon ?: 'delete', $caption ?: _('Delete'));
		$this->action = $action;
		$this->message = $message ?: _('This operation is destructible. Are you sure you want to continue?');
	}
}
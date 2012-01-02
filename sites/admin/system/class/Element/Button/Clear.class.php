<?
namespace Element\Button;

class Clear extends \Element\Button
{
	public function __construct($action = 'new', $caption = null, $icon = null, $message = null)
	{
		parent::__construct(null, $icon ?: 'page_white_star', $caption ?: _('Ny'));
		$this->action = $action;
		$this->message = $message;
	}
}
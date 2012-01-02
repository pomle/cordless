<?
namespace Element\Button;

class Delete extends \Element\Button
{
	public function __construct($action = 'delete', $caption = null, $icon = null, $message = null)
	{
		parent::__construct(null, $icon ?: 'delete', $caption ?: _('Radera'));
		$this->action = $action;
		$this->message = $message ?: _('Detta förstör sparad data. Är du säker på att du vill fortsätta?');
	}
}
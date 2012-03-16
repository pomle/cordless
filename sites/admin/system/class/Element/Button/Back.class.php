<?
namespace Element\Button;

class Back extends \Element\Button
{
	public function __construct($href = null, $caption = null, $icon = null, $message = null)
	{
		parent::__construct($href, $icon ?: 'arrow_left', $caption ?: _('Back'));
		$this->message = $message;
	}
}
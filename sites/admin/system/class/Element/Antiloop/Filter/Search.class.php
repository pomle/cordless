<?
namespace Element\Antiloop\Filter;

class Search extends Text
{
	public static function text()
	{
		$Search = new self();
		$Search->caption = _('Sök');
		return $Search;
	}


	public function __construct()
	{
		parent::__construct('search', 'magnifier', _('Sök'));
		$this->size = 32;
		$this->classes[] = 'search';
		$this->currentString = '';
	}
}
<?
namespace Element\Antiloop\Filter;

class Slice extends Common\Root
{
	public static function limit($isShowAllSelectable = false)
	{
		$options = array(5, 10, 20, 30, 40, 50, 100, 200, 500, 1000, 2000, 3000, 4000, 5000);

		$Limit = new self();
		$Limit->limit['icon'] = 'text_list_numbers';
		$Limit->limit['caption'] = _('Antal rader');

		$Limit->limit['options'] = $isShowAllSelectable ? array(0 => _('Alla')) : array();
		$Limit->limit['options'] += array_combine($options, $options);

		return $Limit;
	}

	public static function page()
	{
		$Page = new self();
		$Page->page['icon'] = 'page_white_stack';
		$Page->page['caption'] = _('Sidnummer');
		return $Page;
	}

	public static function pagination($isShowAllSelectable = false)
	{
		$Slice = new self();

		$Limit = self::limit($isShowAllSelectable);
		$Slice->limit = $Limit->limit;

		$Page = self::page();
		$Slice->page = $Page->page;

		return $Slice;
	}


	private function __construct()
	{
	}

	public function __toString()
	{
		$string = '';

		if( isset($this->limit) )
		{
			$string .=
				(string)\Element\Icon::custom($this->limit['icon'], $this->limit['caption']) .
				(string)\Asenine\Element\SelectBox::keyPair('filter[limit]', $this->limit['selectedKey'], $this->limit['options'])->addClass('limit');
		}

		if( isset($this->page) )
		{
			$string .=
				(string)\Element\Icon::custom($this->page['icon'], $this->page['caption']) .
				'<a href="#" class="prevPage pD" rel="-1">' . (string)\Element\Icon::custom('mono_minus', _('Föregående sida')) . '</a>' .
				(string)\Asenine\Element\Input::text('filter[page]', (int)$this->page['currentPage'] ?: 1)->size(4)->addClass('page') .
				'<a href="#" class="nextPage pD" rel="1">' . (string)\Element\Icon::custom('mono_plus', _('Nästa fält')) . '</a>' .
				'<a href="#" class="clear pD">' . (string)\Element\Icon::custom('textfield_delete', _('Rensa fält')) . '</a>';
		}

		return $string;
	}


	public function disallowAll()
	{

	}


	public function importParams(array $params)
	{
		if( isset($this->limit) ) $this->limit['selectedKey'] = isset($params['limit']) ? (int)$params['limit'] : \Element\Antiloop::DEFAULT_LIMIT;
		if( isset($this->page) ) $this->page['currentPage'] = isset($params['page']) ? (int)$params['page'] : \Element\Antiloop::DEFAULT_PAGE;
	}
}
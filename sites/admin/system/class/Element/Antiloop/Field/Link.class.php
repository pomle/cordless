<?
namespace Element\Antiloop\Field;

class Link extends Common\Root
{
	public static function ajaxCall(\AjaxCall $AjaxCall, $params, $icon, $caption)
	{
		$Field = new self($caption, $icon, trim((string)$AjaxCall, '&'), $params);
		$Field->class[] = 'pD';
		$Field->isAjaxCall = true;
		return $Field;
	}

	public static function custom($headIcon, $headCaption, $headHref, $rowIcon, $rowCaption, $rowHref, $rowParams)
	{
		$Field = new self($rowCaption, $rowIcon, $rowHref, $rowParams);
		$Field->setHeadAction($headHref, $headIcon, $headCaption);
		return $Field;
	}

	public static function owner($name, $href, array $params = array())
	{
		$Field = new self(_('Redigera'), 'page_edit', $href, $params);
		$Field->setHeadAction($href, 'add', _('Skapa ny') . '...');
		return $Field;
	}


	public function __construct($caption, $icon, $href, array $params = array())
	{
		parent::__construct(null, $caption, $icon);

		$this->isAjaxCall = false;
		$this->isSortable = false;
		$this->class = array();
		$this->href = $href . ( strpos($href, '?') ? '&' : '?');
		$this->params = $params;

		$this->setContentHandler(
			function($value, $Field, $dataRow)
			{
				$Icon = \Element\Icon::custom($Field->icon, $Field->caption);

				$href = $Field->href;

				foreach($Field->params as $key => $param)
				{
					if( is_numeric($key) )
						$href .= sprintf('%s=%s&', $param, $dataRow[$param]);
					else
						$href .= sprintf('%s=%s&', $key, $param);
				}

				$content = sprintf('<a href="%s" class="%s">%s</a>', htmlspecialchars($href), join(' ', $Field->class), $Icon);
				if( $Field->isAjaxCall ) $content .= '<img src="/layout/ajax_dot_loader.gif" class="loader">';
				return $content;
			}
		);
	}


	public function setHeadAction($href, $icon, $caption)
	{
		$this->headHref = $href;
		$this->headIcon = $icon;
		$this->headCaption = $caption;

		$this->setHeadHandler(
			function($Field)
			{
				return sprintf('<a href="%s">%s</a>', $Field->headHref, \Element\Icon::custom($Field->headIcon, $Field->headCaption));
			}
		);

		return $this;
	}
}
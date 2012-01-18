<?
namespace Element\Antiloop;

class Field extends Field\Common\Root
{
	public static function ajaxLoad($protocol, Array $params)
	{
		$AjaxCall = new \AjaxCall($protocol, array('action' => 'load'), '/ajax/AjaxRequest.php');
		$Field = Field\Link::ajaxCall($AjaxCall, $params, 'folder', _('Öppna').'...');
		$Field->class[] = 'ajaxLoad';
		return $Field;
	}

	public static function blank($name, $caption = null, $icon = null, $class = null)
	{
		return Field\Blank::raw($name, $caption, $icon, $class);
	}

	public static function bool($name, $caption, $icon, $iconMap = null)
	{
		if( is_null($iconMap) )
		{
			$iconMap = array
			(
				'1' => array('icon' => 'accept_tiny', 'caption' => _('Ja')),
				'default' => array('icon' => 'cancel_tiny', 'caption' => _('Nej'))
			);
		}

		$Field = Field\Map::icon($name, $caption, $icon, $iconMap);
		$Field->isSortReversed = true;
		return $Field;
	}

	public static function button($caption, $icon, $href, array $params)
	{
		$Field = new Field\Link($caption, $icon, $href, $params);
		return $Field;
	}

	public static function count($name, $caption = null, $icon = 'text_list_numbers')
	{
		return Field\Number::integer($name, $caption ?: _('Antal'), $icon);
	}

	public static function creator($href, $params)
	{
		$Field = Field\Link::custom('add', _('Skapa').'...', $href, 'page_edit', _('Redigera').'...', $href, $params);
		return $Field;
	}

	public static function currency($name, $caption, $icon = 'money')
	{
		$Field = Field\Number::currency($name, $caption, $icon, '%!.2n');
		$Field->class = 'currency';
		return $Field;
	}

	public static function date($name, $caption, $icon = 'calendar', $numDecimals = 2)
	{
		return Field\Time::date($name, $caption, $icon);
	}

	public static function download($href, $params, $icon = null)
	{
		$Field = new Field\Link(_('Ladda ner').'...', $icon ?: 'page_white_download', $href, $params);
		return $Field;
	}

	public static function enabled($name = 'isEnabled', $caption = null, $icon = 'eye')
	{
		return self::bool($name, $caption ?: _('Visas'), $icon);
	}

	public static function examiner($href, $params,  $icon = null)
	{
		$Field = new Field\Link(_('Undersök').'...', $icon ?: 'magnifier', $href, $params);
		return $Field;
	}

	public static function icon($name, $caption, $icon, $iconMap)
	{
		return Field\Map::icon
		(
			$name,
			$caption ?: _('Land/Språk'),
			$icon,
			$iconMap
		);
	}

	public static function id($name, $caption = 'ID', $icon = 'database')
	{
		return new Field\ID($name, $caption, $icon);
	}

	public static function ip($name, $caption = null, $icon = null)
	{
		return Field\IP::v4DotNotation($name, $caption ?: _('IP-adress'), $icon ?: 'computer_key');
	}

	public static function link($caption, $href, $params = null, $icon = null)
	{
		$Field = new Field\Link($caption, $icon ?: 'link_go', $href, $params ?: array());
		return $Field;
	}

	public static function locale($name = 'localeID', $caption = null, $icon = 'world')
	{
		$locales = \Manager\Dataset\Locale::getIdent();

		$iconMap = array('default' => array('icon' => 'help', 'caption' => _('Okänt/Ospecificerat')));
		foreach($locales as $localeID => $locale)
			$iconMap[(string)$localeID] = array('icon' => 'flags/' . $locale['ident'], 'caption' => $locale['country']);

		return self::icon($name, $caption, $icon, $iconMap);
	}

	public static function text($name, $caption, $icon = 'text_dropcaps')
	{
		return Field\Text::raw($name, $caption, $icon);
	}

	public static function thumb($name = 'mediaHash', $caption = null, $icon = 'image')
	{
		return Field\Media::thumb($name, $caption ?: _('Bild'), $icon);
	}

	public static function time($name, $caption = null, $icon = 'time')
	{
		return Field\Time::stamp($name, $caption ?: _('Tidslag'), $icon);
	}

	public static function url($name = 'url', $caption = null, $icon = null)
	{
		return self::text($name, $caption ?: _('Länk'), $icon ?: 'world_link');
	}

	public static function user($name = null, $caption = null, $icon = null)
	{
		return self::text($name ?: 'username', $caption ?: _('Användare'), $icon ?: 'user_orange');
	}

	public static function volume($name, $caption = null, $icon = 'layout')
	{
		return Field\Unit::liter($name, $caption ?: _('Volym (l)'), $icon);
	}
}
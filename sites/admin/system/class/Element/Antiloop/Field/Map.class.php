<?
namespace Element\Antiloop\Field;

class Map extends Common\Root
{
	public static function icon($name, $caption, $icon, array $map)
	{
		$Map = new self($name, $caption, $icon, $map);

		$Map->setContentHandler(
			function($value, $Field, $dataRow)
			{
				if( isset($Field->map[$value]) )
				{
					return (string)\Element\Icon::custom($Field->map[$value]['icon'], $Field->map[$value]['caption']);
				}
				elseif( isset($Field->map['default']) )
				{
					return (string)\Element\Icon::custom($Field->map['default']['icon'], $Field->map['default']['caption']);
				}
				return htmlspecialchars($value);
			}
		);

		return $Map;
	}

	public static function string($name, $caption, $icon, array $map)
	{
		$Map = new self($name, $caption, $icon, $map);

		$Map->setContentHandler(
			function($value, $Field, $dataRow)
			{
				if( isset($Field->map[$value]) )
				{
					return htmlspecialchars($Field->map[$value]);
				}
				return htmlspecialchars($value);
			}
		);

		return $Map;
	}


	public function __construct($name, $caption, $icon, array $map = array())
	{
		parent::__construct($name, $caption, $icon);
		$this->map = $map;
	}
}
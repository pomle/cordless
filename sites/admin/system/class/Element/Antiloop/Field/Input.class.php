<?
namespace Element\Antiloop\Field;

class Input extends Common\Root
{
	public static function checkbox($name, $caption = null, $icon = null, $isCheckedValue = null, $inputName = null)
	{
		$Field = new self($name, $caption ?: _('Invertera markering'), $icon ?: 'contrast');
		$Field->inputName = $inputName ?: $name;

		$Field->isCheckedValue = $isCheckedValue;

		$Field->setHeadHandler(
			function($Field)
			{
				return '<a href="#" class="invertCheck pD">' . \Element\Icon::custom($Field->icon, $Field->caption) . '</a>';
			}
		);

		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				$isChecked = (isset($dataRow[$Field->isCheckedValue]) && $dataRow[$Field->isCheckedValue]);
				return \Element\Input::checkbox($Field->inputName . '[]', $isChecked, $value);
			}
		);

		return $Field;
	}

	public static function text($name, $caption, $icon, $inputName = null)
	{
		$Field = new self($name, $caption, $icon);
		$Field->inputName = $inputName ?: $name;
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				$isChecked = ($value == $isCheckedValue);
				return \Element\Input::radio($Field->inputName . '[]', $value);
			}
		);
		return $Field;
	}

	public static function radio($name, $caption = null, $icon = null, $isCheckedValue = null, $inputName = null)
	{
		$Field = new self($name, $caption, $icon);
		$Field->inputName = $inputName ?: $name;
		$Field->setContentHandler(
			function($value, $Field, $dataRow)
			{
				$isChecked = ($value == $isCheckedValue);
				return \Element\Input::radio($Field->inputName . '[]', $isChecked, $value);
			}
		);
		return $Field;
	}


	public function __construct($name, $caption, $icon)
	{
		parent::__construct($name, $caption, $icon);
		$this->isSortable = false;
	}
}
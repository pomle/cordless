<?
namespace Element;

class Tag
{
	public static function legend($icon = null, $caption = null)
	{
		return (isset($icon) ? Icon::custom($icon, $caption) : '') . htmlspecialchars($caption);
	}
}
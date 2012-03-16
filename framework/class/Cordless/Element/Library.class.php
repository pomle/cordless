<?
namespace Cordless\Element;

class Library
{
	public static function head($h1 = null, $h2 = null, $t = null)
	{
		$html = sprintf('<div class="header" data-title="%s">', htmlspecialchars($t ?: $h1));
		if( $h1 ) $html .= '<h1>' . htmlspecialchars($h1) . '</h1>';
		if( $h2 ) $html .= '<h2>' . htmlspecialchars($h2) . '</h2>';
		$html .= '</div>';

		return $html;
	}
}
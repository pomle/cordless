<?
namespace Asenine\PDFPage\Common;

interface PDF
{
	public function addPagesToBatch(\PDF $PDF);
}


abstract class Root implements PDF
{
	const PAGE_ORIENTATION = 'P';
	const PAGE_FORMAT = 'A4';

	const FONT_FACE = 'Arial';

	const PAGE_WIDTH = 210;
	const PAGE_HEIGHT = 297;
	const PAGE_MARGIN_X = 15;
	const PAGE_MARGIN_Y = 15;


	final public static function stringConv($string, $charset = "UTF-8")
	{
		return iconv($charset, "ISO-8859-1//TRANSLIT", $string);
	}


	final public function addBarCode(\PDF $PDF, $data)
	{
		$PDF->Code39(168, 287, $data, 0.8, 12);
	}

	final public function addBarCodeText(\PDF $PDF, $text)
	{
		$PDF->SetFont('Arial', '', 6);
		$PDF->Text(158, 286.5, self::stringConv($text));
	}



	public function addToBatch(\PDF $PDF)
	{
	}

	final protected function replaceText($PDF, $from, $to)
	{
		foreach($PDF->pages as &$pageContent)
			$pageContent = str_replace((array)$from, $to, $pageContent);

		reset($PDF->pages);
	}
}
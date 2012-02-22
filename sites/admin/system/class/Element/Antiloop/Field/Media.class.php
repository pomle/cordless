<?
namespace Element\Antiloop\Field;

class Media extends Common\Root
{
	public static function thumb($name = 'mediaHash', $caption = null, $icon = 'image')
	{
		return new self($name, $caption, $icon, 50, 50, true);
	}

	public static function wide($name = null, $caption = null, $icon = null)
	{
		return new self($name, $caption, $icon, 150, 50, false);
	}


	public function __construct($name = null, $caption = null, $icon = null, $sizeX = 40, $sizeY = 40, $crop = false)
	{
		parent::__construct($name ?: 'mediaHash', $caption ?: _('Förhandsgranskning'), $icon ?: 'image');

		$this->sizeX = abs($sizeX);
		$this->sizeY = abs($sizeY);
		$this->crop = (bool)$crop;

		$this->setContentHandler(
			function($value, $Field, $dataRow)
			{
				if( strlen($value) != 32 ) return false;

				if( isset($dataRow['mediaType']) && $dataRow['mediaType'] == MEDIA_TYPE_AUDIO )
					return false;

				if( $Field->crop )
					$Preset = new \Media\Generator\Preset\CroppedThumb($value, $Field->sizeX, $Field->sizeY);
				else
					$Preset = new \Media\Generator\Preset\AspectThumb($value, $Field->sizeX, $Field->sizeY);

				$imageURL = $Preset->getURL();

				return sprintf('<a href="%5$s" class="media" style="height: %4$upx; width: %3$upx;"><img src="%1$s" title="%2$s" alt="%2$s"></a>',
					$imageURL ?: URL_FALLBACK_THUMB_ICON,
					htmlspecialchars($value),
					$Field->sizeX,
					$Field->sizeY,
					htmlspecialchars('/helpers/sendFile/Media.php?mediaHash=' . $value)
				);
			}
		);
	}
}
<?
namespace Element;

global $css;
$css[] = '/css/page/Media.css';

class MediaPreview
{
	public function __construct(\Media $Media)
	{
		$this->Media = $Media;
	}

	public function __toString()
	{
		ob_start();

		$Media = $this->Media;

		if( $Media::VARIANT == 'visual' )
		{
			//$thumbURL = \Media\Producer\CrossSite::createFromMedia($Media)->getThumb() ?: URL_FALLBACK_THUMB;
			$thumbURL = $Media->mediaID ? sprintf('/helpers/liveGen/Thumb.php?mediaID=%u', $Media->mediaID) : null;

			if( $thumbURL )
			{
				?>
				<div class="preview" style="width:200px;">
					<div style="background-image: url('<? echo htmlspecialchars($thumbURL); ?>'); height:200px;"></div>
				</div>
				<?
			}

			if( $Media::TYPE == MEDIA_TYPE_IMAGE && $imageInfo = $Media->getInfo() )
			{
				$x = $imageInfo['size']['x'];
				$y = $imageInfo['size']['y'];

				$MediaInfo = new \Element\Table();
				echo $MediaInfo
					->addRow(_('Format'), $imageInfo['format'] ?: TEXT_NOT_AVAILABLE)
					->addRow(_('Dimensions'), $x && $y ? sprintf('%u x %u', $x, $y) : TEXT_NOT_AVAILABLE)
					->addRow(_('Aspect Ratio'), $x && $y ? sprintf('%.3f', $x / $y) : TEXT_NOT_AVAILABLE);
			}

			if( $Media::TYPE == MEDIA_TYPE_VIDEO )
			{
				//$thumbURL = \Media\Producer\CrossSite::createFromMedia($Media)->getThumb() ?: URL_FALLBACK_THUMB;
				$stripURL = $Media->mediaID ? sprintf('/helpers/liveGen/VideoStrip.php?mediaID=%u&frames=5&size=100', $Media->mediaID) : null;

				if( $stripURL )
				{
					?>
					<div class="preview" style="width:500px;">
						<div style="background-image: url('<? echo htmlspecialchars($stripURL); ?>'); height:100px;"></div>
					</div>
					<?
				}

				$downloads[] = array(sprintf('/helpers/liveGen/VideoStrip.php?mediaID=%u', $Media->mediaID), 'Frame Strip');

				if( $Media::TYPE == MEDIA_TYPE_VIDEO )
				{
					$streamInfo = $Media->getInfo();

					$MediaInfo = new \Element\Table();
					echo $MediaInfo
						->addRow(_('Dimensions'), sprintf('%u x %u', $streamInfo['video']['size']['x'], $streamInfo['video']['size']['y']))
						->addRow(_('Aspect Ratio'), sprintf('%.3f', $streamInfo['video']['aspect']['display']))
						->addRow(_('Duration'), $streamInfo['time']['c'])
						->addRow(_('Frames'), $streamInfo['video']['frames'])
						->addRow(_('FPS'), $streamInfo['video']['fps']);
				}
			}
		}

		return ob_get_clean();
	}
}
<?
namespace Asenine\Media\Generator;

class ImageResize extends \Asenine\Media\Generator
{
	protected $ImageGuru;

	public
		$sizeX,
		$sizeY,
		$cropX,
		$cropY;


	public function __construct(\Asenine\Media\Type\_Visual $Media, $width = null, $height = null, $cropToFill = false, $quality = 100, $format = 'JPG', $allowUpScale = false)
	{
		$this->ImageGuru = new \Asenine\App\ImageGuru();
		$this->ImageGuru->addInput($Media->getPreviewImage());

		$imageInfo = $this->ImageGuru->getImageInfo();

		$orientation = $imageInfo['orientation'];

		#printf("0 if dividable by 90: %d\n", $orientation % 90);
		#$Media->orientation = 90;

		if( $orientation % 90 === 0 ) ### We only fix orientation if 90 degress increments
		{
			$this->rotate = $orientation;

			#printf("0 if dividable by 180: %d\n", $this->rotate % 180);

			if( $this->rotate % 180 !== 0 ) ### Means we convert landscape <=> portrait
			{
				$w = $width;
				$h = $height;
				$width = $h;
				$height = $w;
				unset($w, $h);
			}
		}

		if( $width || $height )
		{
			### Original Dimensions
			$origX = (int)$imageInfo['size']['x'];
			$origY = (int)$imageInfo['size']['y'];

			### Behavior for "shrink to fit"
			$this->sizeX = $width = (int)abs($width);
			$this->sizeY = $height = (int)abs($height);

			if( !$allowUpScale )
			{
				$width = $this->sizeX = min($origX, $this->sizeX);
				$height = $this->sizeY = min($origY, $this->sizeY);
			}

			### Only if we get sane dimensions from original file
			if( $origX && $origY )
			{
				$origAspect = $origX / $origY;

				### If any size is zero, replace with corresponding to keep aspect ratio
				if( $this->sizeX == 0 )
					$this->sizeX = round($this->sizeY * $origAspect);

				if( $this->sizeY == 0 )
					$this->sizeY = round($this->sizeX / $origAspect);


				$newAspect = $this->sizeX / $this->sizeY;

				if( $newAspect > $origAspect )
				{
					###printf("X is %u and too big\n", $this->sizeX);
					$this->sizeX = round($this->sizeY * $origAspect);
				}

				if( $newAspect < $origAspect )
				{
					###printf("Y is %u and too big\n", $this->sizeY);
					$this->sizeY = round($this->sizeX / $origAspect);
				}

				if( $cropToFill )
				{
					if( !$width || !$height ) trigger_error(__METHOD__ . ' called with $cropToFill set but without $width and $height set', E_USER_WARNING);

					$this->cropX = $width;
					$this->cropY = $height;

					if( $this->sizeX < $this->cropX )
					{
						$scaleFactor = $this->cropX / $this->sizeX;
						$this->sizeX = $this->cropX;
						$this->sizeY = round($this->sizeY * $scaleFactor);
					}

					if( $this->sizeY < $this->cropY )
					{
						$scaleFactor = $this->cropY / $this->sizeY;
						$this->sizeY = $this->cropY;
						$this->sizeX = round($this->sizeX * $scaleFactor);
					}
				}
			}
		}

		$this->quality = (int)$quality;
		$this->format = $format;
	}


	public function saveToFile($outFile)
	{
		if( $this->sizeX || $this->sizeY )
		{
			$options[] = '-flatten';
			$options[] = '+repage';
			$options[] = sprintf('-resize \'!%ux%u\'', $this->sizeX, $this->sizeY);

			if( $this->cropX && $this->cropY && ($this->sizeX <> $this->cropX) || ($this->sizeY <> $this->cropY) )
			{
				$options[] = sprintf('-gravity Center -crop %ux%u+0+0', $this->cropX, $this->cropY);
			}
		}

		if( $this->rotate ) $options[] = sprintf('-rotate %d', $this->rotate);

		$options[] = sprintf('-quality %u', $this->quality);

		$this->ImageGuru->setFormat($this->format);

		return $this->ImageGuru->writeFile($outFile, $options);
	}
}
<?
namespace Media\Generator;

class Rotate extends _Generator
{
	protected $ImageGuru;

	public
		$sizeX,
		$sizeY,
		$cropX,
		$cropY;


	public function __construct(



		if( !$this->isInitiated ) {

			if( !$this->workDir = \FileManager::getTempDir('Media_Rotate_') ) throw New \Exception('Could not get workdir');

			if( !is_dir($this->workDir) ) throw New \Exception('Workdir was not created');

			if( !is_writeable($this->workDir) ) throw New \Exception('Workdir not writeable');

			if( !class_exists('ZipArchive') ) throw New \Exception("Zip Archive class missing");

			try {

				$Zip = new \ZipArchive();

				$this->cleanWorkDir();

				if( $Zip->open($this->sourceFile) !== true ) throw New \Exception('Not a valid Zip Archive');

				$this->ImageHandler = new \AppController\ImageManipulator();

				$originalNames = array();

				// Get the original intended order
				for($i = 1; $i <= $Zip->numFiles; $i++) {

					if( !$fileInfo = $Zip->statIndex($i-1) ) throw New \Exception('Error on Archive Analyze');

					$originalNames[] = $fileInfo['name'];
				}

				natsort($originalNames);


				// Save with clean filenames to avoid problems in cmd
				$fileIndex = 0;
				foreach($originalNames as $originalName) {
					$filename = sprintf('rotate_%03d', $fileIndex);

					// Get file based on original name
					$content = $Zip->getFromName($originalName);

					// Put file with safe name
					file_put_contents($this->workDir . $filename, $content);

					$fileIndex++;
				}

				unset($Zip);

				$frames = glob($this->workDir . '*');

				// Check if file count is the same as intended, otherwise cleanup has failed or some other process has put other files in the work dir
				if( count($frames) <> self::FRAME_COUNT ) {
					throw New \Exception("Wrong Image Count\nFiles in Working Dir:\n" . join("\n", $frames));
				}

				natsort($frames);

				$this->frames = $frames;

				$this->hash	= md5_file($this->sourceFile);

				// If all tests are OK
				$this->isSourceValid = true;
				$this->isInitiated = true;

			}catch(\Exception $e) {

				$this->isSourceValid = false;
				$this->isInitiated = false;

				$this->cleanWorkDir();
			}
		}


	public function __construct(\Media\Common\Visual $Media, $width = null, $height = null, $cropToFill = false, $quality = 100, $format = 'JPG', $allowUpScale = false)
	{
		$this->ImageGuru = new \App\ImageGuru();
		$this->ImageGuru->addInput($Media->getPreviewImage());

		if( $width || $height )
		{
			// Behavior for "shrink to fit"
			$this->sizeX = $width = (int)abs($width);
			$this->sizeY = $height = (int)abs($height);

			// Original Dimensions
			$imageInfo = $this->ImageGuru->getImageInfo();

			$origX = (int)$imageInfo['size']['x'];
			$origY = (int)$imageInfo['size']['y'];

			if( !$allowUpScale )
			{
				$this->sizeX = min($origX, $this->sizeX);
				$this->sizeY = min($origY, $this->sizeY);
			}

			// Only if we get sane dimensions from original file
			if( $origX && $origY )
			{
				$origAspect = $origX / $origY;

				// If any size is zero, replace with corresponding to keep aspect ratio
				if( $this->sizeX == 0 )
					$this->sizeX = round($this->sizeY * $origAspect);

				if( $this->sizeY == 0 )
					$this->sizeY = round($this->sizeX / $origAspect);


				$newAspect = $this->sizeX / $this->sizeY;

				if( $newAspect > $origAspect )
				{
					//printf("X is %u and too big\n", $this->sizeX);
					$this->sizeX = round($this->sizeY * $origAspect);
				}

				if( $newAspect < $origAspect )
				{
					//printf("Y is %u and too big\n", $this->sizeY);
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
			$options[] = sprintf('-resize \'!%ux%u\'', $this->sizeX, $this->sizeY);

			if( $this->cropX && $this->cropY && ($this->sizeX <> $this->cropX) || ($this->sizeY <> $this->cropY) )
			{
				$options[] = sprintf('-gravity Center -crop %ux%u+0+0', $this->cropX, $this->cropY);
			}
		}

		$options[] = sprintf('-quality %u', $this->quality);

		$this->ImageGuru->setFormat($this->format);

		return $this->ImageGuru->writeFile($outFile, $options);
	}
}
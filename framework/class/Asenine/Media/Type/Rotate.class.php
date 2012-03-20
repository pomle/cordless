<?
namespace Asenine\Media\Type;

class Rotate extends _Visual
{
	const TYPE = ASENINE_MEDIA_TYPE_ROTATE;
	const DESCRIPTION = '360 Image';

	protected
		$Zip,
		$files,
		$frames;


	public static function canHandleFile($filePath)
	{
		if( !$Zip = self::getZip($filePath) ) return false;
		if( $Zip->numFiles != 36 ) return false;
		return true;
	}

	private static function getZip($filePath)
	{
		if( !is_file($filePath) )
			return false;

		if( !@class_exists('ZipArchive') )
		{
			#trigger_error('Class ZipArchive does not exists', E_USER_NOTICE);
			return false;
		}

		$Zip = new \ZipArchive();

		if( $Zip->open($filePath) !== true ) return false;

		return $Zip;
	}


	public function __destruct()
	{
		if( isset($this->frames) )
			foreach($this->frames as $frame)
				unlink($frame);
	}


	public function getFrame($index)
	{
		if( !isset($this->frames[$index]) )
		{
			if( !isset($this->Zip) )
				if( !$this->Zip = self::getZip($this->getFilePath()) ) return false;

			if( !$this->getInfo() )
				return false;

			if( !isset($this->files[$index]) )
				return false;

			if( !$content = $this->Zip->getFromName($this->files[$index]) )
				return false;

			$tempFile = \Asenine\getTempFile('Rotate_Frame');

			if( !file_put_contents($tempFile, $content) )
				return false;

			$this->frames[$index] = $tempFile;
		}
		return $this->frames[$index];
	}

	public function getFrameCount()
	{
		$this->getInfo();
		return count($this->files);
	}

	public function getFrames()
	{
		$info = $this->getInfo();
		$frames = array();

		foreach($info['files'] as $i => $filename)
			$frames = $this->getFrame($i);

		return $frames;
	}

	public function getInfo()
	{
		if( !isset($this->files) )
		{
			if( !isset($this->Zip) )
				if( !$this->Zip = self::getZip($this->getFilePath()) ) return false;

			$files = array();

			for($i = 0; $i < $this->Zip->numFiles; $i++)
			{
				$file = $this->Zip->statIndex($i);
				$files[$i] = $file ? $file['name'] : null;
			}

			natsort($files);
			$files = array_values($files);

			$this->files = $files;
		}

		return array
		(
			'count' => count($this->files),
			'files' => $this->files
		);
	}

	public function getPreviewImage()
	{
		return $this->getFrame(0);
	}
}
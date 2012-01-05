<?
namespace Media;

class Image extends Common\Visual
{
	const TYPE = MEDIA_TYPE_IMAGE;
	const DESCRIPTION = 'Image / Graphic';

	public static function canHandleFile($filePath)
	{
		return \App\ImageGuru::isValidFile($filePath);
	}

	public static function createFromFile($filePath)
	{
		if( $Image = parent::createFromFile($filePath) )
		{
			$info = $Image->getInfo();
			$Image->orientation = $info['orientation'];
			return $Image;
		}

		return false;
	}


	public function getFrame($index = 0)
	{
		return $this->getFilePath();
	}

	public function getFrameCount()
	{
		return 1;
	}

	public function getInfo()
	{
		return \App\ImageGuru::doIdentify($this->getFilePath(), true);
	}

	public function getPreviewImage()
	{
		return $this->getFrame();
	}
}
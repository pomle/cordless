<?
namespace Asenine\Media\Type;

class Image extends _Visual
{
	const TYPE = ASENINE_MEDIA_TYPE_IMAGE;
	const DESCRIPTION = 'Image / Graphic';

	public static function canHandleFile($filePath)
	{
		return \Asenine\App\ImageGuru::isValidFile($filePath);
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
		return \Asenine\App\ImageGuru::doIdentify($this->getFilePath(), true);
	}

	public function getPreviewImage()
	{
		return $this->getFrame();
	}
}
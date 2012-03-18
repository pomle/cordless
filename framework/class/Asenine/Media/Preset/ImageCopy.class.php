<?
namespace Asenine\Media\Preset;

class ImageCopy extends \Asenine\Media\Preset
{
	const NAME = 'imageCopy';

	protected
		$sourceFile;

	public function __construct($mediaHash)
	{
		$this->mediaHash = $mediaHash;

		if( !$Media = \Manager\Media::loadByHash($this->mediaHash) )
			throw New \Exception(__METHOD__ . ' could not load media from database for file type identification');

		if( !$Media instanceof \Media\Image )
			throw New \Exception(__METHOD__ . ' got wrong Media type');

		if( !$imageInfo = $Media->getInfo() )
			throw New \Exception(__METHOD__ . ' could not get image format');

		switch($imageInfo['format'])
		{
			case 'GIF':
				$this->ext = '.gif';
			break;

			case 'JPEG':
				$this->ext = '.jpg';
			break;

			case 'PNG':
				$this->ext = '.png';
			break;
		}

		if( empty($this->ext) )
			throw New \Exception(__METHOD__ . ' could not interpret file extention from format "' . $imageInfo['format'] . '"');

		$this->sourceFile = $Media->getFilePath();
	}

	public function createFile($filepath)
	{
		$destinationFile = $filepath;

		return copy($this->sourceFile, $destinationFile);
	}
}
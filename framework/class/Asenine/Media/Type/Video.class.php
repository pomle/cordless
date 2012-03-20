<?
namespace Asenine\Media\Type;

class Video extends _Visual
{
	const TYPE = ASENINE_MEDIA_TYPE_VIDEO;
	const DESCRIPTION = 'Video';

	protected
		$VideoFrame,
		$streamInfo,
		$previewImageFile;

	public static function canHandleFile($filePath)
	{
		if( !$streamInfo = \Asenine\App\FFMPEG::doInfo($filePath) )
			return false;

		if( is_null($streamInfo['video']) )
			return false;

		return true;
	}


	public function __destruct()
	{
		if( isset($this->previewImageFile) && file_exists($this->previewImageFile) ) unlink($this->previewImageFile);
	}

	public function getDuration()
	{
		if( $streamInfo = $this->getInfo() )
			return isset($streamInfo['duration']) ? (int)abs($streamInfo['duration']) : null;

		return false;
	}

	public function getFrame($index)
	{
		if( !isset($this->VideoFrame) )
			$this->VideoFrame = new \Asenine\Media\Generator\VideoFrame($this);

		$this->VideoFrame->seekFrame(floor(abs($index)));

		### If getting tempfile and frame exctraction succeeds, return filename, or clean up if needed
		if( $tempFrame = \Asenine\getTempFile('Media_Video_Frame_') )
		{
			if( $this->VideoFrame->saveToFile($tempFrame) )
				return $tempFrame;
			else
				unlink($tempFrame);
		}
		return false;
	}

	public function getFrameCount()
	{
		if( $streamInfo = $this->getInfo() )
			return isset($streamInfo['video']['frames']) ? (int)abs($streamInfo['video']['frames']) : 0;

		return false;
	}

	public function getInfo()
	{
		if( !isset($this->streamInfo) )
			$this->streamInfo = \Asenine\App\FFMPEG::doInfo($this->getFilePath());

		return $this->streamInfo;
	}

	public function getPreviewImage()
	{
		if( !isset($this->previewImageFile) )
			$this->previewImageFile = $this->getFrame($this->getFrameCount() / 2);

		return $this->previewImageFile;
	}
}
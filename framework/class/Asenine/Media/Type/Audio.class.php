<?
namespace Asenine\Media\Type;

class Audio extends _Audible
{
	const TYPE = ASENINE_MEDIA_TYPE_AUDIO;
	const DESCRIPTION = 'Audio';

	protected
		$streamInfo;

	public static function canHandleFile($filePath)
	{
		if( !$streamInfo = \Asenine\App\FFMPEG::doInfo($filePath) )
			return false;

		### Reject streams without Audio
		if( is_null($streamInfo['audio']) ) return false;

		### Reject streams with Video
		if( !is_null($streamInfo['video']) ) return false;

		return true;
	}


	public function __destruct()
	{
	}


	public function getInfo()
	{
		if( !isset($this->streamInfo) )
			$this->streamInfo = \Asenine\App\FFMPEG::doInfo($this->File);

		return $this->streamInfo;
	}
}
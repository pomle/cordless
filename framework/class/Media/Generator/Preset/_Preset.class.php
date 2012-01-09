<?
namespace Media\Generator\Preset;

interface _Interface
{
	public function createFile();
}

abstract class _Preset implements _Interface
{
	protected
		$mediaHash,
		$subPath,
		$ext;

	final public function getFile()
	{
		if( !$this->isGenerated() )
		{
			$dirPath = DIR_MEDIA . $this->getPath();
			if( !file_exists($dirPath) && !is_dir($dirPath) && !mkdir($dirPath, 0755, true) ) throw New \Exception("Path not reachable \"$dirPath\"");
			if( !$this->createFile() ) return false;
		}

		return $this->getFilePath();
	}

	final public function getFileName()
	{
		return $this->mediaHash . $this->ext;
	}

	final public function getFilePath()
	{
		return $this->getPath() . $this->getFileName();
	}

	final public function getFullFilePath()
	{
		return DIR_MEDIA . $this->getFilePath();
	}

	final public function getMediaHash()
	{
		return $this->mediaHash;
	}

	final public function getPath()
	{
		return 'autogen/preset/' . static::NAME . '/' . $this->subPath;
	}

	final public function getURL()
	{
		try
		{
			$fileName = $this->getFileName();
			$path = $this->getPath();

			if( !$this->getFile() ) return false;

			return URL_MEDIA . $path . $fileName;
		}
		catch(\Exception $e)
		{
			trigger_error(get_called_class() . ' media generation failed, Reason: ' . $e->getMessage(), E_USER_WARNING);
			return false;
		}
	}

	final public function isGenerated()
	{
		$diskFile = DIR_MEDIA . $this->getFilePath();
		return file_exists($diskFile);
	}
}
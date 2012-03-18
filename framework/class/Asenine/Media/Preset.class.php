<?
namespace Asenine\Media;

interface iPreset
{
	public function createFile($filepath);
}

abstract class Preset implements iPreset
{
	protected
		$mediaHash,
		$subPath,
		$ext;

	final public function getFile($wait = true)
	{
		$fileExists = $this->isGenerated();

		if( !$fileExists )
		{
			$sleepTime = 100000; // 100 ms


			$dirPath = DIR_MEDIA_PUBLIC . $this->getPath();
			if( !file_exists($dirPath) && !is_dir($dirPath) && !mkdir($dirPath, 0755, true) ) throw New \Exception("Path not reachable \"$dirPath\"");

			$filePath = $this->getFullFilePath();


			if( !$resource = fopen($filePath, "c") )
				throw New \Exception("Could not create handle for \"$filePath\"");

			while( true )
			{
				$haveLock = flock($resource, LOCK_EX | LOCK_NB, $wouldblock);

				if( $haveLock )
				{
					clearstatcache($filePath);

					$fileSize = filesize($filePath);

					if( $fileSize == 0 ) ### A 0 byte fileSize means that we are the creator
					{
						ftruncate($resource, 0);

						if( !$this->createFile($filePath) )
							trigger_error(get_class($this) . "::createFile() returned false for $filePath", E_USER_WARNING);
					}

					flock($resource, LOCK_UN);

					$fileExists = true;

					break;
				}

				if( $wait )
					usleep($sleepTime);
				else
					break;
			}

			fclose($resource);
		}

		return $fileExists ? $this->getFilePath() : false;
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
		return DIR_MEDIA_PUBLIC . $this->getFilePath();
	}

	final public function getMediaHash()
	{
		return $this->mediaHash;
	}

	final public function getPath()
	{
		return 'autogen/preset/' . static::NAME . '/' . $this->subPath;
	}

	final public function getURL($wait = true)
	{
		try
		{
			if( !$this->getFile($wait) ) return false;

			return URL_MEDIA . $this->getFilePath();
		}
		catch(\Exception $e)
		{
			trigger_error(get_called_class() . ' media generation failed, Reason: ' . $e->getMessage(), E_USER_WARNING);
			return false;
		}
	}

	final public function isGenerated()
	{
		$diskFile = $this->getFullFilePath();
		return ( file_exists($diskFile) && filesize($diskFile) > 0 );
	}
}
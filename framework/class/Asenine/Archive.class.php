<?
namespace Asenine;

asenineDef('ARCHIVE_DIR_DEPTH', 4);
asenineDef('ARCHIVE_DIR_SPLIT_LEN', 2);
asenineDef('ARCHIVE_PERM_CREATE', 0755);

class ArchiveException extends \Exception{}

class Archive
{
	protected
		$treePath,
		$workPath;


	public function __construct($namespace, $subpath = null)
	{
		if( !file_exists(DIR_ARCHIVE) || !is_dir(DIR_ARCHIVE) || !is_writeable(DIR_ARCHIVE) )
			throw New ArchiveException('"' . DIR_ARCHIVE . '" is not a valid and writeable dir');

		if( strlen($namespace) == 0 )
			throw New ArchiveException('Required arg #1 missing from ' . __METHOD__);

		if( preg_match('%[^A-Za-z/]%', $namespace) )
			throw New ArchiveException("Archive namespace argument ($namespace) contains illegal characters");

		$this->namespace = (string)$namespace;

		$this->treePath = trim($this->namespace, '/') . '/';

		if( $subpath ) $this->treePath .=  trim($subpath, '/') . '/';

		$this->workPath = DIR_ARCHIVE . $this->treePath;
	}


	public function getFile($hash)
	{
		return new File( $this->getFileName($hash), $hash );
	}

	public function getFileName($hash)
	{
		return $this->getFilePath($hash) . $hash;
	}

	public function getFilePath($hash)
	{
		return $this->workPath . $this->resolveHash($hash);
	}

	public function putFile(File $File, $overwrite = false)
	{
		$hash = $File->hash;

		$inputFileName = (string)$File;

		$archiveFilePath = $this->getFilePath($hash);

		if( file_exists($archiveFilePath) )
		{
			if( !is_dir($archiveFilePath) )
				throw New ArchiveException(sprintf('"%s" already exists and is not a dir', $archiveFilePath));

			if( !is_writeable($archiveFilePath) )
				throw New ArchiveException(sprintf('"%s" is not writeable', $archiveFilePath));
		}
		elseif( !mkdir($archiveFilePath, ARCHIVE_PERM_CREATE, true) )
			throw New ArchiveException(sprintf('Could not create dir "%s"', $archiveFilePath));


		$archiveFileName = $this->getFileName($hash);

		if( $overwrite !== true && file_exists($archiveFileName) )
		{
			if( $hash !== md5_file($archiveFileName) )
				throw new ArchiveException(sprintf('File "%s" already exists', $archiveFileName));
		}
		elseif( !copy($inputFileName, $archiveFileName) )
			throw New ArchiveException(sprintf('Could not copy "%s" to "%s"', $inputFileName, $archiveFileName));

		$ArchivedFile = new File($archiveFileName);
		$ArchivedFile->archiveHash = $hash;

		return $ArchivedFile;
	}


	protected function resolveHash($hash)
	{
		$path = '';

		$i = 0;
		while($i < ARCHIVE_DIR_DEPTH)
			$path .= substr($hash, $i++ * ARCHIVE_DIR_SPLIT_LEN, ARCHIVE_DIR_SPLIT_LEN) . '/';

		return $path;
	}
}
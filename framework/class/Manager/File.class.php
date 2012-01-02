<?
namespace Manager;

class File
{
	public function getTempDir($prefix = null)
	{
		$tmpFile = self::getTempFile($prefix);
		if( !unlink($tmpFile) || !mkdir($tmpFile) ) return false;
		return $tmpFile;
	}

	public function getTempFile($prefix = null)
	{
		return tempnam(DIR_TEMP, $prefix ? $prefix . '_' : null);
	}
}
<?
require __DIR__ . '/../../Init.inc.php';

function yes($a)
{
	$a = trim($a);
	return (strlen($a) == 1 && strpos('Yy', $a) !== false);
}

function no($a)
{
	$a = trim($a);
	return (strlen($a) == 1 && strpos('Nn', $a) !== false);
}


list(, $src, $dst) = $argv;

$src = realpath($src);
$dst = realpath(ASENINE_DIR_ARCHIVE);

if( !file_exists($src) || !is_dir($src) || !is_readable($src) )
	die("'$src' is not a valid, readable dir");

if( !file_exists($dst) || !is_dir($dst) || !is_writeable($dst) )
	die("'$dst' is not a valid, writeable dir");

for(;;)
{
	fwrite(STDOUT, sprintf("This will COPY files from %s to %s. Continue? [Y/n]: ", $src, $dst));

	$res = fgets(STDIN);

	if( no($res) )
		die("Aborted by user\n");

	if( yes($res) )
		break;
}

$src_Source = new DirectoryIterator($src . '/source/');
$dst_Source = new \Asenine\Archive('media/source/');

foreach($src_Source as $pFile)
{
	if( $pFile->isFile() )
	{
		$sourceFile = $pFile->getPathname();
		$File = new \Asenine\File($sourceFile);
		$NewFile = $dst_Source->putFile($File);

		$destFile = $NewFile->location;

		printf("Copied %s to %s\n", $sourceFile, $destFile);
	}
}
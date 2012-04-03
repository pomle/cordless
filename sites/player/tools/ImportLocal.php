<?
if( !defined('STDOUT') || !defined('STDIN') )
	die("This program is a CLI script only");

require __DIR__ . '/_Common.inc.php';

$src = isset($argv[1]) ? $argv[1] : '.';

$src = realpath($src);
$dst = realpath(ASENINE_DIR_ARCHIVE);

fwrite(STDOUT, "This script will import files recursively from a local folder and assign to a user. Please log in to continue.\n");


if( !file_exists($src) || !is_dir($src) || !is_readable($src) )
	die("'$src' is not a valid, readable dir");

if( !file_exists($dst) || !is_dir($dst) || !is_writeable($dst) )
	die("'$dst' is not a valid, writeable dir");

fwrite(STDOUT, "Username: ");
$username = trim(fgets(STDIN), "\n");


fwrite(STDOUT, "Password: ");
system('stty -echo');
$password = trim(fgets(STDIN), "\n");
system('stty echo');
fwrite(STDOUT, "\n");

if( !$User = \Cordless\User::login($username, $password) )
	die("User authentication failed\n");

for(;;)
{
	fwrite(STDOUT, sprintf("This will SYMLINK files from %s to %s and import to user %s. Continue? [Y/n]: ", $src, $dst, $User->username));

	$res = fgets(STDIN);

	if( no($res) )
		die("Aborted by user\n");

	if( yes($res) )
		break;
}

$SourceDir = new RecursiveDirectoryIterator($src);
$Iterator = new RecursiveIteratorIterator($SourceDir);

$i = 0;

foreach($Iterator as $pFile)
{
	try
	{
		if( $pFile->isFile() )
		{
			$sourceFile = $pFile->getPathname();

			$File = new \Asenine\File($sourceFile, null, true);
			$File->isCopySymlink = true;

			$UserTrack = \Cordless\Event\UserTrack::importFile($User, $File);

			printf("Imported file \"%s\" as \"%s\"\n", $sourceFile, $UserTrack);

			if( $i++ > 10 ) break;
		}
	}
	catch(\Exception $e)
	{
		printf("Import of \"%s\" failed, Reason: %s\n", $sourceFile, $e->getMessage());
		if( $i++ > 10 ) break;
	}
}
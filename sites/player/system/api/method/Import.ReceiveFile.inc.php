<?
namespace Cordless;

function APIMethod($User, $params)
{
	if( !$User->hasPolicy('AllowCordlessUpload') )
		throw new APIException(_("Upload denied by policy"));

	if( !isset($_FILES) || !is_array($_FILES) || !count($_FILES) )
		throw new APIException(_("No files received"));

	try
	{
		foreach($_FILES as $file)
		{
			$File = \Asenine\File::fromPHPUpload($file);
			$UserTrack = Event\UserTrack::importFile($User, $File);

			break;
		}
	}
	catch(\Exception $e)
	{
		throw new APIException($File->name . ": " . $e->getMessage());
	}

	return (string)$UserTrack;
}
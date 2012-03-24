<?
namespace Cordless;

function APIMethod($User, $params)
{
	if( !$User->hasPolicy('AllowCordlessUpload') )
		throw new APIException(_("Upload denied by policy"));

	if( !isset($_FILES) || !is_array($_FILES) || !count($_FILES) )
		throw new APIException(_("No files received"));

	foreach($_FILES as $file)
	{
		$fileName = $file['name'];

		try
		{
			if( empty($file['tmp_name']) )
				throw new APIException(_('File data empty'));

			$File = \Asenine\File::fromPHPUpload($file);
			$UserTrack = Event\UserTrack::importFile($User, $File);
		}
		catch(\Exception $e)
		{
			throw new APIException($fileName . ": " . $e->getMessage());
		}

		break;
	}

	return (string)$UserTrack;
}
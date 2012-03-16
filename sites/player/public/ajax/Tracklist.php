<?
namespace Cordless;

require '../../Init.Web.inc.php';

try
{
	ob_start();

	if( isset($_GET['fetcher']) && $Obj = json_decode($_GET['fetcher']) )
	{
		$Fetcher = new Fetch\UserTrack($User, $Obj->method);

		foreach($Obj as $key => $value)
			$Fetcher->$key = $value;

		if( isset($_GET['skipWhat']) )
		{
			switch($_GET['skipWhat'])
			{
				case 'page':
					$Fetcher->pageSkip(isset($_GET['skipAmount']) ? (int)$_GET['skipAmount'] : 1);
				break;
			}
		}

		echo Element\Tracklist::createFromFetcher( $Fetcher )->getItemsHTML();
	}

	ob_end_flush();
}
catch(\Exception $e)
{
	ob_end_clean();

	die($e->getMessage());
}

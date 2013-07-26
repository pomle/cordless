<?
namespace Cordless;

use \Asenine\DB;

try
{
	if( !isset($params->playlistID) || !$Playlist = Playlist::loadFromDB($params->playlistID) )
		throw New \Exception('Invalid Playlist');

	echo Element\Library::head($Playlist->title);

	$Fetch = new Fetch\UserTrack($User, 'byPlaylist', $Playlist->playlistID);

	$userTracks = $Fetch();

	if( count($userTracks) == 0 )
		throw New \Exception(_("Playlist seems empty"));

	echo Element\Tracklist::createFromUserTracks($userTracks);

	?>
	<script type="text/javascript">
		<?
		if(isset($params->startPlaying) && $params->startPlaying)
		{
			?>
			$('.tracklist .control .queueReplace').click();
			Cordless.Interface.playqueueLock();
			<?
		}
		?>
	</script>
	<?
}
catch(\Exception $e)
{
	echo Element\Message::error($e->getMessage());
}
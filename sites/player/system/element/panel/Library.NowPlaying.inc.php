<?
namespace Cordless;

use \Asenine\DB;

echo Element\Library::head(_("Now Playing"));
?>
<div id="nowPlaying">
	<div class="canvas">
		<div class="image">

		</div>
	</div>

	<div class="time"><? echo formatDuration(0); ?></div>

	<h1 class="title"><? echo _("Please start playing..."); ?></h1>
	<a class="artist" target="last_fm_artist"></a>

	<!--
	<ul>
		<li><? printf(_("Listeners: %s"), '<span class="lastfm_artist_listeners"></span>'); ?></li>
		<li><? printf(_("Plays: %s"), '<span class="lastfm_artist_playcount"></span>'); ?></li>
	</ul>
	-->

	<?
	if( $User->last_fm_username )
	{
		?>
		<!--
		<ul>
			<li><?
				printf(
					_("You have scrobbled this track %s times and played this artist %s times"),
					'<span class="lastfm_user_track_scrobbles"></span>',
					'<span class="lastfm_user_artist_scrobbles"></span>');
			?></li>
		</ul>
		-->
		<?
	}
	?>

	<div class="bio">
		<div class="summary"></div>

		<a href="" class="readMore" target="last_fm_artist"><? echo _("Read more"), ' &raquo;'; ?></a>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var newElements = $('#nowPlaying:not(.isInitiated)');

		if( newElements.length )
		{
			Cordless.NowPlaying.isEnabled = true;
			Cordless.NowPlaying.eNowPlaying = Cordless.NowPlaying.eNowPlaying.add(newElements).addClass('isInitiated');
			Cordless.NowPlaying.update();

			var ePlayer = $('#player');

			ePlayer.find('.time .current').off('.nowPlaying').on('onUpdate.nowPlaying', function()
			{
				Cordless.NowPlaying.updateTime( $(this).text() );
			});

			ePlayer.off('.nowPlaying').on('onTrackLoaded.nowPlaying', function()
			{
				Cordless.NowPlaying.update();
			});
		}
	});
</script>
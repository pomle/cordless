$(function()
{
	var
		Player = Cordless.Player,
		Library = Cordless.Library;

	$('#playqueue')
		// Jump to Track in PlayQueue
		.on("click", ".userTrack .title a, .userTrack .image", function(e) {
			e.preventDefault();
			var userTrack = $(this).closest('.userTrack');
			Player.trackLoadItem(userTrack);
			Player.playbackStart();
		})

		.on("click", ".userTrack .artist a", function(e) {
			e.preventDefault();
			var artist = $(this).closest('.userTrack').data('artist');
			Library.goTo('Tracks-Artist', {'artist': artist});
		})

		// Playlist Shuffle
		.on("click", ".playlist>.control .shuffle", function(e) {
			e.preventDefault();
			var P = new PlaylistController($(this).closest('.playlist'));
			P.shuffle();
		})

		// Playlist Save
		.on("click", ".playlist>.control .save", function(e) {
			e.preventDefault();

			var
				ePlaylist = $(this).closest('.playlist'),
				playlistTitle_current = ePlaylist.data('playlisttitle'),
				playlistTitle = playlistTitle_current,
				playlistID = ePlaylist.data('playlistid'),
				userTracks = ePlaylist.find('.userTracks .userTrack');

			if( userTracks.length == 0 && playlistID && confirm("Delete playlist \"" + playlistTitle_current + "\"?") )
			{
				Cordless.API.addCall(
					'Playlist',
					{'action': 'delete', 'playlistID': playlistID},
					function(response) {
						if(response.status)
						{
							ePlaylist
								.data('playlistid', null)
								.data('playlisttitle', null)
								;
						}
					}
				);
			}

			if( userTracks.length > 0 && ( playlistTitle = prompt("Playlist title", playlistTitle || '') ) )
			{
				if( playlistTitle_current != playlistTitle )
					playlistID = null;

				var userTrackIDs = [];

				userTracks.each(function(i, userTrack) {
					var userTrackID = $(userTrack).data('usertrackid');
					userTrackIDs.push(userTrackID);
				});

				Cordless.API.addCall(
					'Playlist',
					{'action': 'save', 'playlistID': playlistID || 0, 'title': playlistTitle, 'userTrackIDs': userTrackIDs},
					function(response) {
						if( response.status )
						{
							ePlaylist
								.data('playlistid', response.data.playlistID)
								.data('playlisttitle', playlistTitle)
								;
						}
					}
				);
			}
		})

		// Playlist Clear
		.on("click", ".playlist>.control .clear", function(e) {
			e.preventDefault();
			var P = new PlaylistController($(this).closest('.playlist'));
			P.clear();
		})

		.on("click", ".userTrack .removeItem", function(e) {
			e.preventDefault();
			$(this).closest('.userTrack').remove();
		})
		;
});



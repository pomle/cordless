$(function()
{
	var
		Player = Cordless.Player,
		PlayQueue = Cordless.PlayQueue,
		Library = Cordless.Library;

	$('#library')
		// User Tracklist as PlayQueue
		.on("click", ".tracklist>.control .queueReplace", function(e) {
			e.preventDefault();
			var userTracks = $(this).closest('.tracklist').find('.userTracks .userTrack').clone();
			PlayQueue.replaceWith(userTracks);
			if( Player.playlistSeek(0) )
				Player.playbackStart();
		})

		// Append Tracklist to PlayQueue
		.on("click", ".tracklist>.control .queueLast", function(e) {
			e.preventDefault();
			var userTracks = $(this).closest('.tracklist').find('.userTracks .userTrack').clone();
			PlayQueue.appendTo(userTracks);
		})

		// Insert Tracklist to PlayQueue after current item
		.on("click", ".tracklist>.control .queueNext", function(e) {
			e.preventDefault();
			var userTracks = $(this).closest('.tracklist').find('.userTracks .userTrack').clone();
			PlayQueue.afterCurrent(userTracks);
		})

		.on("click", ".tracklist>.toggleView", function(e) {
			e.preventDefault();
			var
				b = $('body'),
				modes = ["tracklistTiles", "tracklistList"],
				cI = 0;

			for(var i = 0; i < modes.length; i++)
			{
				var c = modes[i];
				if( b.hasClass(c) ) cI = i + 1; //
				b.removeClass(c);
			}

			var cIn = cI % modes.length;
			var cN = modes[cIn];
			b.addClass(cN);
			userSetting('WebUI_Tracklist_View_Mode', cN);
		})

		.on("click", ".tracklist .fetchMore", function(e) {
			e.preventDefault();
			var eTracks = $(this);
			var fetcher = eTracks.attr('data-fetcher');

			$.ajax({
				'url': './ajax/Tracklist.php?skipWhat=page&skipAmount=1&fetcher=' + encodeURIComponent(fetcher),
				'type': 'GET',
				'data': fetcher,
				'dataType': 'html',
				'success': function(response)
				{
					eTracks.replaceWith(response);
					$(window).scrollTop( $('body').height() );
				}
			});
		})

		// Set track as current item
		.on("click", ".userTrack>.control .queuePlay", function(e) {
			e.preventDefault();
			var userTrack = $(this).closest('.userTrack').clone();
			PlayQueue.afterCurrent(userTrack);
			if( Player.trackLoadItem(userTrack) )
				Player.playbackStart();
		})

		// Set track as next item
		.on("click", ".userTrack>.control .queueNext", function(e) {
			e.preventDefault();
			var userTrack = $(this).closest('.userTrack').clone();
			PlayQueue.afterCurrent(userTrack);
		})

		// Append Library Track to PlayQueue
		.on("click", ".userTrack>.control .queueLast", function(e) {
			e.preventDefault();
			var userTrack = $(this).closest('.userTrack').clone();
			PlayQueue.appendTo(userTrack);
		})

		// GoTo Track Page
		.on("click", ".userTrack .title", function(e) {
			var userTrackID = $(this).closest('.userTrack').data('usertrackid');

			if( e.ctrlKey )
				window.location = "./api/?method=Stream&userTrackID=" + userTrackID;
			else
				Library.goTo('UserTrack-Control', {'userTrackID': userTrackID});
		})

		// GoTo Artist Page
		.on("click", ".userTrack .artist", function(e) {
			var artist = $(this).closest('.userTrack').data('artist');
			Library.goTo('Tracks-Artist', {'artist': artist});
		});
		;
});
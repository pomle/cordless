$(function()
{
	$(document)
		// User Tracklist as PlayQueue
		.on("click", ".userTrack .starToggle", function(e) {
			e.preventDefault();

			var eUserTrack = $(this).closest('.userTrack');
			var userTrackID = eUserTrack.data('usertrackid');
			eUserTrack.toggleClass('isStarred');

			Cordless.API.addCall(
				'UserTrack.Star',
				{'userTrackID': userTrackID, 'isStarred': Number(eUserTrack.hasClass('isStarred'))},
				function(response)
				{
					var userTrackID;
					if( userTrackID = response.data.userTrackID )
					{
						var eUserTracks = $('.userTrack.id' + userTrackID);
						if( response.data.isStarred )
							eUserTracks.addClass('isStarred');
						else
							eUserTracks.removeClass('isStarred');
					}
				}
			);
		})

		.on("click", ".userTrackPlay", function(e) {
			e.preventDefault();

			var userTrackID = $(this).data('usertrackid');

			Cordless.API.makeCall(
				'UserTrack.HTML',
				{'userTrackIDs': userTrackID},
				function(response)
				{
					var userTrack = $(response.data[userTrackID]);
					Cordless.PlayQueue.afterCurrent(userTrack);
					if( Cordless.Player.trackLoadItem(userTrack) )
						Cordless.Player.playbackStart();
				}
			);
		})
		;
});
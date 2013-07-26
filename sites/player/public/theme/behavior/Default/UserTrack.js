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
				function(data)
				{
					var userTrackID;
					if( userTrackID = data.userTrackID )
					{
						var eUserTracks = $('.userTrack.id' + userTrackID);
						if( data.isStarred )
							eUserTracks.addClass('isStarred');
						else
							eUserTracks.removeClass('isStarred');
					}
				}
			);
		})

		.on("click", ".userTrack .takeOwnership", function(e) {
			e.preventDefault();

			var eUserTrack = $(this).closest('.userTrack');
			var userTrackID = eUserTrack.data('usertrackid');

			eUserTrack.addClass('isBusy');

			Cordless.API.makeCall(
				'UserTrack.Edit',
				{'action': 'grab', 'userTrackID': userTrackID, 'returnHTML': true},
				function(data)
				{
					var userTrackHTML = data.userTrackHTML;
					eUserTrack.replaceWith(userTrackHTML);
				}
			);
		})

		.on("click", ".userTrackPlay", function(e) {
			e.preventDefault();

			var userTrackID = $(this).data('usertrackid');

			Cordless.API.makeCall(
				'UserTrack.HTML',
				{'userTrackIDs': userTrackID},
				function(data)
				{
					var userTrack = $(data[userTrackID]);
					Cordless.PlayQueue.afterCurrent(userTrack);
					if( Cordless.Player.trackLoadItem(userTrack) )
						Cordless.Player.playbackStart();
				}
			);
		})
		;
});
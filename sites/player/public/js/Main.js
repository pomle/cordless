function CordlessController()
{
	var api_url = $('#cordless').data('cordless-api-url');
	var ajax_url = $('#cordless').data('cordless-ajax-url');

	this.AJAX_URL = ajax_url;
	this.API_URL = api_url;

	this.LAST_FM_API_KEY = $('#cordless').data('last-fm-api-key') || null;
	this.LAST_FM_API_URL = this.LAST_FM_API_KEY ? 'http://ws.audioscrobbler.com/2.0/?api_key=' + this.LAST_FM_API_KEY : null;

	this.API		= new APIController( api_url );
	this.Interface	= new InterfaceController( $('#player'), $('#playqueue'), $('#upload') );
	this.Library	= new PanelController('Library', $('#library>.content'), $('#library>.history>.trail'));
	this.PlayQueue	= new PlaylistController( $('#playqueue').find('.playlist') );
	this.Player		= new AudioController( this.PlayQueue, api_url );
	this.NowPlaying = new NowPlayingController( this.Player );



	this.getState = function()
	{
		var state = {
			'playingIndex': null,
			'playingCurrently': Cordless.Player.isPlaying,
			'userTrackIDs': []
		};

		var userTracks = this.PlayQueue.getItems();

		userTracks.each(function() {
			state['userTrackIDs'].push($(this).data('usertrackid'));
		});

		var playingIndex = userTracks.filter('.isCurrent').index();
		if (playingIndex > -1) {
			state['playingIndex'] = playingIndex;
		}

		return state;
	}
}


try
{
	var Cordless = new CordlessController();


	$(function()
	{
		var
			goToURL;

		window.onbeforeunload = function(e)
		{
			if( Cordless.Player.isPlaying )
			{
				var msg = 'Sound will stop';

				e.returnValue = msg;
				return msg;
			}
		}

		window.onunload = function(e)
		{
			Cordless.API.makeCall('User.State', Cordless.getState());
		}

		if( goToURL = $('#library').data('gotourl') ) {
			Cordless.Library.goToURL(goToURL);
		}
		else {
			Cordless.API.makeCall('User.State', null, function(state) {
				
				if (!state || !state.userTrackIDs) {
					return false;
				}
				
				Cordless.API.makeCall('UserTrack.HTML', {'userTrackIDs': state.userTrackIDs}, function(data) {

					for (index in state.userTrackIDs) {
						Cordless.PlayQueue.appendTo(data[state.userTrackIDs[index]]);
					}

					if (state.playingIndex) {
						var userTrack = Cordless.PlayQueue.itemSeek(state.playingIndex);
						if (Cordless.Player.trackLoadItem(userTrack)) {
	
							if (state.playingCurrently) {
								Cordless.Player.playbackStart();
							}
						}
					}					
				});
			});
		}
	
	});
}
catch (e)
{
	alert("Sorry. Your browser does not seem to support Cordless.\nError reported: " + e.message);
}


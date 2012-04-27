$(function()
{
	var eCordless = $('#player');
	var eProgressBar = eCordless.find('.scrubber .progress');
	var eTrackPosition = eCordless.find('.time .current');
	var eTrackDuration = eCordless.find('.time .total');

	var eTrackTitle = eCordless.find('.trackinfo .title');
	var eTrackArtist = eCordless.find('.trackinfo .artist');
	var eTrackError = eCordless.find('.trackinfo .error');

	var
		isNextTrackChecked = false,
		isNextTrackReady = false;

	// "Global" functions
	function updateProgressBar(value)
	{
		value = Math.min(Math.abs(value), 1) * 100;
		eProgressBar.css('width', value + '%');
	}

	// Cordless event bindings
	Cordless.Player.eventDurationChanged = function(Track)
	{
		eTrackDuration.text( formatDuration(Track.duration) ).trigger('onUpdate');
	}

	Cordless.Player.eventPlaybackStarted = function()
	{
		eCordless.addClass('isPlaying');
	}

	Cordless.Player.eventPlaybackStopped = function()
	{
		eCordless.removeClass('isPlaying');
	}

	Cordless.Player.eventPlayerError = function()
	{
		eTrackError.text('Error: ' + Cordless.Player.playingURL);
	}

	Cordless.Player.eventTimeChanged = function(Track)
	{
		updateProgressBar(Track.progress);

		var time = formatDuration(Track.position);
		if( eTrackPosition.text() != time )
			eTrackPosition.text(time).trigger('onUpdate');

		if( !isNextTrackChecked && Track.progress > 0.75 )
		{
			var
				eUserTrack,
				userTrackID;

			isNextTrackChecked = true;
			if( eUserTrack = Cordless.PlayQueue.itemNext() )
			{
				var userTrackID = eUserTrack.data('usertrackid');

				Cordless.API.makeCall(
					'Stream',
					{'userTrackID': userTrackID, 'prepare': 1},
					function(response) {
						isNextTrackReady = response.data.isPrepared;
					}
				);
			}
		}
	}

	Cordless.Player.eventTrackEnded = function(Track)
	{
		registerPlay(Track);
	}

	Cordless.Player.eventTrackLoaded = function(Track, userTrack)
	{
		isNextTrackReady = false;
		isNextTrackChecked = false;

		eTrackTitle.html(Track.artist + ' - ' + Track.title);
		eTrackError.text('');

		eCordless
			.addClass('isBusy')
			.data('playing-artist', Track.artist)
			.data('playing-title', Track.title)
			.trigger('onTrackLoaded')
			;

		userTrack.addClass('isCurrent').siblings().removeClass('isCurrent');

		eCordless.find('.playingUserTrack').html(userTrack.clone());
	}

	Cordless.Player.eventTrackReady = function()
	{
		eCordless.removeClass('isBusy').addClass('isReady');
		console.log('Track Ready');
	}


	Cordless.Player.eventTrackStarted = function(Track)
	{
		console.log('Track Started');
		eCordless.trigger('onTrackStarted');
		Cordless.API.addCall('UserTrack.NowPlaying', Track);
	}

	Cordless.Player.eventTrackUnloaded = function(Track)
	{
		eCordless.removeClass('isReady');

		eTrackPosition.text( formatDuration(0) ).trigger('onUpdate');
		eTrackDuration.text( formatDuration(0) ).trigger('onUpdate');

		eTrackTitle.text('');
		eTrackArtist.text('');
		eTrackError.text('');

		updateProgressBar(0);
	}



	eCordless.find('.play_pause').on('click', function(e)
	{
		e.preventDefault();
		Cordless.Player.playbackToggle();
	});

	eCordless.find('.prev').on('click', function(e)
	{
		e.preventDefault();
		Cordless.Player.playlistPrev();
	});

	eCordless.find('.next').on('click', function(e)
	{
		e.preventDefault();
		Cordless.Player.playlistNext();
	});

	eCordless.find('.scrubber').on('mousedown', function(e)
	{
		var pos = e.pageX - $(this).offset().left;
		var max = $(this).width();

		Cordless.Player.playbackSeekLocation(pos / max);
	})
	.on('click', function(e)
	{
		e.preventDefault();
	});
});
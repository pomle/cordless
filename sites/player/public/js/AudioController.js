function AudioController( PlayQueue , api_url )
{
	var self = this;

	this.isPlaying = false; // Contains the state the player is in, not the actual Audio
	this.isTrackLoaded = false;
	this.isTrackReady = false;
	this.isTrackStarted = false;

	this.playingArtist = null;
	this.playingTitle = null;
	this.playingUserTrackID = null;
	this.playingURL = null;

	this.trackPlayedTime = null; // Counts how long playback has been active (non-paused) on the current track
	this.trackStartTime = null; // When the current track started playing

	// The Audio object
	var channels = [ new Audio() ];
	var oAudio = channels[0];

	this.Audio = oAudio;

	var
		timerTrackReady,
		timerMainLoop;


	oAudio.onabort = function() // Seem to only trigger when Audio.src changes, which I am are
	{
		//self.trackUnload();
	}

	// Only event I could get to work reliably cross browser
	oAudio.onerror = function(error)
	{
		self.playbackStop();
		self.trackUnload();
		self.eventPlayerError();
	}


	this.eventDurationChanged = function()
	{}

	this.eventPlaybackStarted = function(Track)
	{}

	this.eventPlaybackStopped = function(Track)
	{}

	this.eventPlayerError = function()
	{}

	this.eventTimeChanged = function(Track)
	{}

	this.eventTrackEnded = function(Track)
	{}

	this.eventTrackLoaded = function(Track, userTrack)
	{}

	this.eventTrackReady = function(Track)
	{}

	this.eventTrackStarted = function(Track)
	{}

	this.eventTrackUnloaded = function(Track)
	{}

	this.eventTrackWaiting = function(Track)
	{}


	this.getTrack = function() // Returns a unified Track object
	{
		var
			duration = isFinite(oAudio.duration) ? oAudio.duration : null,
			position = isFinite(oAudio.currentTime) ? oAudio.currentTime : null,
			progress = (duration > 0 && position) ? position / duration : null

		return {
			'userTrackID': this.playingUserTrackID,
			'artist': this.playingArtist,
			'title': this.playingTitle,
			'playedTime': this.trackPlayedTime,
			'startTime': this.trackStartTime,
			'duration': duration,
			'position': position,
			'progress': progress,
			'url': oAudio.src
		};
	}

	var mainLoop = function()
	{
		this.lastTime = this.thisTime || new Date().getTime();
		this.thisTime = new Date().getTime();

		//console.log(this.lastTime, this.thisTime, this.thisTime - this.lastTime);
		//console.log(oAudio.networkState);
		if( self.isTrackReady && self.isPlaying )
		{
			if( !self.isTrackStarted )
			{
				self.isTrackStarted = true;
				self.trackStartTime = (this.thisTime / 1000);
				self.eventTrackStarted( self.getTrack() );
			}

			//console.log('Counting Time');

			self.trackPlayedTime += (this.thisTime - this.lastTime) / 1000;

			self.eventTimeChanged( self.getTrack() );

			if( oAudio.ended )
			{
				if( !self.playlistNext() )
				{
					self.playbackStop();
					self.trackEnd();
				}
			}
		}

		this.lastTime = this.thisTime;
	}

	this.playlistNext = function()
	{
		return self.playlistSkip(1);
	}

	this.playlistPrev = function()
	{
		return self.playlistSkip(-1);
	}

	this.playlistSeek = function(index)
	{
		var
			wasPlaying = this.isPlaying,
			userTrack;

		if( userTrack = PlayQueue.itemSeek(index) )
			return self.trackLoadItem(userTrack);

		return false;
	}

	this.playlistSkip = function(diff)
	{
		var userTrack;

		if( userTrack = PlayQueue.itemSkip(diff) )
			return self.trackLoadItem(userTrack);
	}

	this.playbackReset = function()
	{
		return ( self.playbackStop() && self.trackUnload() );
	}

	this.playbackSeekLocation = function(value)
	{
		if( !isFinite(value) )
			return false;

		value = parseFloat(value);

		if( value < 0 || value > 1 )
			return false;

		return self.playbackSeekTime(oAudio.duration * value);
	}

	this.playbackSeekTime = function(seconds)
	{
		if( !this.isTrackLoaded ) return false;

		try
		{
			var goalTime = Math.min(seconds, oAudio.duration);
			oAudio.currentTime = goalTime; // Will throw an error if not available
			this.eventTimeChanged( this.getTrack() );

			return true; // (oAudio.currentTime == goalTime); maybe?
		}
		catch (e)
		{
			console.log(e);
			return false;
		}
	}

	this.playbackStart = function()
	{
		if( !this.isTrackLoaded && !this.playlistSkip(0) )
			return false;

		oAudio.play();

		this.isPlaying = true;
		this.eventPlaybackStarted( this.getTrack() );

		return true;
	}

	this.playbackStop = function()
	{
		oAudio.pause();

		this.isPlaying = false;
		this.eventPlaybackStopped( this.getTrack() );

		return true;
	}

	this.playbackToggle = function()
	{
		if( this.isPlaying )
			return this.playbackStop();
		else
			return this.playbackStart();
	}

	this.trackLoadItem = function(userTrack)
	{
		var
			wasPlaying = this.isPlaying,
			userTrackID = userTrack.data('usertrackid');

		if( !userTrackID )
			return false;

		this.playbackReset();

		if( this.trackLoadURL( api_url + '?method=Stream&userTrackID=' + userTrackID ) )
		{
			this.isTrackLoaded = true;

			this.playingArtist = userTrack.data('artist');
			this.playingTitle = userTrack.data('title');
			this.playingUserTrack = userTrack;
			this.playingUserTrackID = userTrackID;

			this.eventTrackLoaded( this.getTrack(), userTrack );

			if( wasPlaying )
				this.playbackStart();

			return true;
		}

		return false;
	}

	this.trackLoadURL = function(url)
	{
		this.trackUnload();

		oAudio.src = url;
		this.playingURL = url;

		var isReadynessChecked = false;

		var readyLoop = function()
		{
			if( isFinite(oAudio.duration) )
			{
				var Track = self.getTrack();

				self.isTrackReady = true;
				self.eventTrackReady( Track );
				self.eventDurationChanged( Track );

				return true;
			}

			if( !isReadynessChecked )
			{
				isReadynessChecked = true;
				self.eventTrackWaiting( self.getTrack() );
			}
			// Check for duration until track is ready every half second as long as track is loaded
			//if( self.isTrackLoaded )
			timerTrackReady = setTimeout(readyLoop, 200);

			return false;
		}

		setTimeout(readyLoop, 500); // Initial wait before going into loop

		return true;
	}

	this.trackEnd = function()
	{
		if( this.isTrackStarted )
		{
			var Track = this.getTrack(); // Save track values before they are reset

			this.isTrackStarted = false;
			this.trackPlayedTime = 0;
			this.trackStartTime = null;

			this.eventTrackEnded(Track);
		}
	}

	this.trackUnload = function()
	{
		if( !this.isTrackLoaded )
			return false;

		var Track = this.getTrack();

		this.isTrackReady = false;
		this.isTrackLoaded = false;

		this.eventTrackUnloaded( Track );
		this.eventDurationChanged( Track );

		this.trackEnd();

		clearTimeout(timerTrackReady);

		return true;
	}

	this.triggerEvent = function(event)
	{
		if( self.eventListeners[event] )
			for(var index in self.eventListeners[event] )
				self.eventListeners[event][index]();
	}

	timerMainLoop = setInterval(mainLoop, 250);
}
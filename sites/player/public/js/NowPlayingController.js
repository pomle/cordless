function NowPlayingController(Player)
{
	var self = this;

	var
		imageTimer = null,
		backgroundTimer = null;

	this.artists = {};
	this.tracks = {};

	this.artistNameCurrent = null;

	this.backgroundIndex = 0;

	this.eNowPlaying = $();

	this.artistAdd = function(artistName, artistData)
	{
		this.artists[artistName] = artistData;
		return true;
	}

	this.artistFetch = function(artistName, callback)
	{
		var isRunning = 0;
		var Artist = {
			'name': artistName,
			'images': [],
			'imagePointer': 0,
			'backgrounds': [],
			'backgroundPointer': 0
		};

		var onComplete = function()
		{
			console.log("isRunning: " + isRunning);
			isRunning--;
			if( isRunning == 0 )
			{
				console.log("Done with " + artistName);
				self.artistAdd(artistName, Artist);
				if( jQuery.isFunction(callback) ) callback(Artist);
			}
		}

		isRunning++;

		$.ajax({
			'type': "GET",
			'url': Cordless.LAST_FM_API_URL + '&method=artist.getinfo&artist=' + encodeURIComponent(artistName),
			'dataType': "xml",
			success: function(xml)
			{
				var xml = $(xml);

				var image = xml.find('lfm>artist>image[size=extralarge]').text();

				var lastfm_artist_listeners = xml.find('lfm>artist>stats>listeners').text();
				var lastfm_artist_playcount = xml.find('lfm>artist>stats>playcount').text();
				var url = xml.find('lfm>artist>url').text();
				var bio = xml.find('lfm>artist>bio>summary').text();

				Artist.name = artistName;
				Artist.lastfm_artist_listeners = lastfm_artist_listeners;
				Artist.lastfm_artist_playcount = lastfm_artist_playcount;
				Artist.url = url;
				Artist.bio = bio;

				if( image ) Artist.images.unshift(image);

			},
			'complete': onComplete
		});

		isRunning++;

		$.ajax({
			'type': "GET",
			'url': Cordless.LAST_FM_API_URL + '&method=artist.getimages&artist=' + encodeURIComponent(artistName),
			'dataType': "xml",
			'success': function(xml)
			{
				var xml = $(xml);

				xml.find('image>sizes').each(function(i)
				{
					var extralarge = $(this).find('size[name=extralarge]').text();

					if( extralarge ) Artist.images.push(extralarge);

					var original = $(this).find('size[name=original]');
					var width = original.attr('width');
					var height = original.attr('height');

					if( width >= 400 && height >= 300 )
					{
						Artist.backgrounds.push( {'url': original.text(), 'width': width, 'height': height} );
					}
				});
			},
			'complete': onComplete
		});
	}

	this.artistImageLoop = function(diff)
	{
		console.log("Running artist imageloop");

		clearTimeout(imageTimer);

		self.artistImageSkip(diff || 0);

		imageTimer = setTimeout(self.artistImageLoop, 1000 * 30);
	}


	this.artistImageSkip = function(diff)
	{
		var Artist = this.getArtistCurrent();
		if( !Artist ) return console.log("No artist found") && false;

		if( Artist.images.length == 0 ) return console.log("Images count 0") && false;

		var index = (Artist.imagePointer + diff) % Artist.images.length;
		if(index < 0) index += Artist.images.length;

		return this.artistImageSeek(Artist, index);
	}

	this.artistImageSeek = function(Artist, index)
	{
		if( !Artist.images[index] ) return console.log("Images Index out of bounds") && false;

		Artist.imagePointer = index;

		var url = Artist.images[Artist.imagePointer];

		return this.artistImageSet(url);
	}

	this.artistImageSet = function(url)
	{
		var eImage = this.eNowPlaying.find('.image');

		//if( !eImage.is(":visible") ) return console.log("Artist Image Canvas not visible") && false;

		var I = new Image();
		I.onload = function()
		{
			eImage.hide().css('background-image', 'url("' + this.src + '")').fadeIn();
		}

		I.src = url;
	}

	this.backgroundLoop = function(diff)
	{
		console.log("Running backgroundLoop");

		clearTimeout(backgroundTimer);

		self.backgroundSkip(diff || 1);

		backgroundTimer = setTimeout(self.backgroundLoop, 1000 * 60 * 2);
	}

	this.backgroundNext = function()
	{
		return this.backgroundSkip(1);
	}

	this.backgroundPrev = function()
	{
		return this.backgroundSkip(-1);
	}

	this.backgroundSkip = function(diff)
	{
		var Artist = this.getArtistCurrent();
		if( !Artist ) return console.log("No artist found") && false;

		if( Artist.backgrounds.length == 0 ) return console.log("Background count 0") && false;

		var index = (Artist.backgroundPointer + diff) % Artist.backgrounds.length;
		if(index < 0) index += Artist.backgrounds.length;

		return this.backgroundSeek(Artist, index);
	}

	this.backgroundSeek = function(Artist, index)
	{
		if( !Artist.backgrounds[index] ) return console.log("Background Index out of bounds") && false;

		Artist.backgroundPointer = index;

		var url = Artist.backgrounds[Artist.backgroundPointer].url;

		return this.backgroundSet(url);
	}

	this.backgroundSet = function(url)
	{
		var body = $('body');

		if( body.hasClass('backgroundLocked') ) return console.log("Background is locked") && false;

		var I = new Image();
		I.onload = function()
		{
			var css = body.css('background-image');
			var urls = css.split(',');
			urls[0] = 'url("' + this.src + '")';
			body.css('background-image', urls.join(',') );
		}

		I.src = url;

		return true;
	}

	this.getArtist = function(artistName)
	{
		return this.artists[artistName] || false;
	}

	this.getArtistCurrent = function()
	{
		return this.getArtist(this.artistNameCurrent);
	}

	this.getElements = function()
	{
		return this.eNowPlaying;
	}

	this.updateContent = function( Track )
	{
		var
			updateArtist = this.updateArtist,
			updateTitle = this.updateTitle;

		var
			userTrackID = Track.userTrackID,
			artist = Track.artist,
			title = Track.title;

		var eNowPlaying = this.getElements();

		var userTrackID_current = eNowPlaying.data('userTrackID');

		if( userTrackID_current == userTrackID )
			return console.log(userTrackID_current + ":UserTrackID not changed") || true;

		var artist_current =  eNowPlaying.data('artist');
		var title_current =  eNowPlaying.data('title');

		if( title_current != title )
		{
			updateTitle( title );
		}

		if( artist_current != artist )
		{
			updateArtist( artist );
		}

		return true;
	}

	this.updateArtist = function(artist)
	{
		var eNowPlaying = self.getElements();

		eNowPlaying.find('.image, .bio').hide().find('.summary').html('');
		eNowPlaying.find('.artist, .readMore').attr('href', '');
		eNowPlaying.find('.artist').text(artist);

		var Artist = self.getArtist(artist);

		if( !Artist )
		{
			self.artistFetch(artist, function() { self.updateArtist(artist); });
			return true;
		}

		eNowPlaying.find('.artist, .readMore').attr('href', Artist.url);

		if( Artist.bio.length > 0 )
		{
			var eBio = eNowPlaying.find('.bio').hide();
			eBio.find('.summary').html( Artist.bio.replace(/\n/g, '<br>') );
			eBio.fadeIn();
		}

		eNowPlaying.find('.lastfm_artist_listeners').text(Artist.lastfm_artist_listeners);
		eNowPlaying.find('.lastfm_artist_playcount').text(Artist.lastfm_artist_playcount);

		self.artistNameCurrent = artist;

		self.artistImageLoop(0);
		self.backgroundLoop(0);

		return true;
	}

	this.updateTitle = function(title)
	{
		var eNowPlaying = self.getElements();
		eNowPlaying.find('.title').text(title);
	}

	this.updateTime = function(text)
	{
		var eTimes = this.eNowPlaying.filter(':visible').find('.time');
		eTimes.text(text);
	}

	this.update = function()
	{
		return this.updateContent( Player.getTrack() );
	}

	this.backgroundLoop(); // Start background loop
}
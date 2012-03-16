/*
	jQuery plugin for drag/drop uploads in HTML5

	Author:
		Pomle
	Email:
		pontus.alexander@gmail.com

	Licensed under:
		Kopimi, no rights reserved

	Project home:
		https://github.com/pomle/jquery-dropUpload

	Version:
		0.5.0

	Usage:
		Examples ahoy
*/

(function( $ ){
	jQuery.event.props.push("dataTransfer");

	var
		isLoopRunning = false,
		loopSize = 0;

	var emptyCallback = function() {};

	var
		settings = {},
		default_settings = {
			'fileDropCountMax': null,
			'fileMeta': emptyCallback,
			'fileParamName': 'dropUploadFile',
			'fileSimTransfers': 1,
			'fileSizeMax': null,

			'onComplete': emptyCallback,
			'onDropError': emptyCallback,
			'onDropSuccess': emptyCallback,
			'onDragEnter': emptyCallback,
			'onDragOver': emptyCallback,
			'onDragLeave': emptyCallback,

			'onFileCompleted': emptyCallback,
			'onFileFailed': function(File, message)
			{
				alert(message);
			},
			'onFileQueued': emptyCallback,
			'onFileSucceeded': emptyCallback,
			'onFileStarted': emptyCallback,

			'onProgressUpdated': emptyCallback,

			'onQueueCompleted': emptyCallback,

			'url': ''
			},
			queue = [];

	var eventDrop = function(e)
	{
		e.preventDefault();

		try
		{
			if( !e.dataTransfer.files || e.dataTransfer.files.length == 0 )
				throw('FILE_ARRAY_EMPTY');

			var FileList = e.dataTransfer.files;

			if( settings.fileDropCountMax && FileList.length > settings.fileDropCountMax )
				throw('FILE_DROP_COUNT_MAX');

			settings.onDropSuccess();
		}
		catch(e)
		{
			settings.onDropError(e.message);
			return false;
		}

		// Iterate over all files and add to queue if isFileAccepted() returns true
		filesIterator(FileList, function(File)
		{
			if( isFileAccepted(File) ) queueFile(File);
		});

		/*
			Engage upload loop if not already running
			Notice that it is allowed to start several instances, but it's recommended to control the simultaneous queue length with fileSimTransfers setting
		*/
		if( !isLoopRunning )
			uploadLoopEngage();

		return true;
	}

	var eventDragEnter = function(e)
	{
		settings.onDragEnter();
	}

	var eventDragLeave = function(e)
	{
		settings.onDragLeave();
	}

	var eventDragOver = function(e)
	{
		e.preventDefault();
		settings.onDragOver();
	}

	// Just a method to disable browser default behavior for certian events
	var eventKillDefault = function(e)
	{
		e.preventDefault();
		return false;
	}

	// Lets us iterate over file lists in a consistent manner
	var filesIterator = function(FileList, callback)
	{
		for(var index = 0; index < FileList.length; index++)
			callback(FileList[index]);

		return true;
	}

	// Returns wheater file is an acceptable upload or not
	var isFileAccepted = function(File)
	{
		if( settings.fileSizeMax && (File.size > settings.fileSizeMax) )
			return false;

		return true;
	}

	var queueFile = function(File)
	{
		File.meta = settings.fileMeta() || {}; // If user function returns any data, put it on the File object

		queue.push(File);

		settings.onFileQueued(File);
	}

	// This function not totally quirk free as of now
	var uploadLoopEngage = function()
	{
		isLoopRunning = true;

		while( queue.length > 0 && loopSize < settings.fileSimTransfers )
		{
			var File = queue.shift();

			try
			{
				loopSize++;
				// uploadLoopEngage is sent as a callback for when the upload completes
				uploadFile(File, uploadLoopEngage);
			}
			catch(e)
			{
				loopSize--;
				// Inform plugin about failure
				settings.onFileFailed(File, e.message);
				settings.onFileCompleted(File);
			}
		}

		isLoopRunning = false;
	}

	var uploadFile = function(File, onCompleteCallback)
	{
		//loopSize++;

		settings.onFileStarted(File);

		var File = File;
		var FR = new FileReader();

		// Defines the call that is made when upload has completed
		var uploadFinished = function()
		{
			loopSize--;

			settings.onProgressUpdated(File, 1);
			settings.onFileCompleted(File);

			if( typeof onCompleteCallback == 'function' )
				onCompleteCallback();

			if( loopSize == 0 )
				settings.onQueueCompleted();
		}

		FR.File = File;
		FR.onload = function(e) // Prepares file and meta data for the POST-stream
		{
			var
				boundary	= '---------------------------7d01ecf406a6'; // Boundary should be a string that is unlikely to occur by chance in the data stream
				dashdash	= '--',
				crlf		= '\r\n',
				data		= '';

			// Instruction for data generation taken from http://www.paraesthesia.com/archive/2009/12/16/posting-multipartform-data-using-.net-webrequest.aspx
			/*
				Generate a "boundary." A boundary is a unique string that serves as a delimiter between each of the form values you'll be sending in your request. Usually these boundaries look something like
					---------------------------7d01ecf406a6
				with a bunch of dashes and a unique value.

				Set the request content type to multipart/form-data; boundary= and your boundary, like:
					multipart/form-data; boundary=---------------------------7d01ecf406a6

				Any time you write a standard form value to the request stream, you'll write:
					Two dashes.
					Your boundary.
					One CRLF (\r\n).
					A content-disposition header that tells the name of the form field you'll be inserting. That looks like:
						Content-Disposition: form-data; name="yourformfieldname"
					Two CRLFs.
					The value of the form field - not URL encoded.
					One CRLF.

				Any time you write a file to the request stream (for upload), you'll write:
					Two dashes.
					Your boundary.
					One CRLF (\r\n).
					A content-disposition header that tells the name of the form field corresponding to the file and the name of the file. That looks like:
						Content-Disposition: form-data; name="yourformfieldname"; filename="somefile.jpg"
					One CRLF.
					A content-type header that says what the MIME type of the file is. That looks like:
					Content-Type: image/jpg
					Two CRLFs.
					The entire contents of the file, byte for byte. It's OK to include binary content here. Don't base-64 encode it or anything, just stream it on in.
					One CRLF.

				At the end of your request, after writing all of your fields and files to the request, you'll write:
					Two dashes.
					Your boundary.
					Two more dashes.
			*/

			// Adds Meta data (connected by user defined function fileMeta()
			$.each(this.File.meta, function(index, meta)
			{
				data += dashdash + boundary + crlf;
				data += 'Content-Disposition: form-data; name="' + meta.name + '"' + crlf + crlf;
				data += meta.value;
				data += crlf;
			});

			// Adds Binary data
			data += dashdash + boundary + crlf;
			data += 'Content-Disposition: form-data; name="' + settings.fileParamName + '"; filename="' + File.name + '"' + crlf;
			data += 'Content-Type: ' + File.type + crlf + crlf;
			data += e.target.result; // e.target.result is the binary data that FileReader() provides
			data += crlf;

			// End delimiter
			data += dashdash + boundary + dashdash;


			var XHR = new XMLHttpRequest();
			XHR.open("POST", settings.url, true); // Perform asynchronous transfer
			XHR.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);


			XHR.onerror = function(e)
			{
				settings.onFileFailed(File);
				uploadFinished();
			};

			XHR.onload = function(e) // Triggers on completed upload
			{
				settings.onFileSucceeded(File, this.responseText); // reponseText is the response body printed by the server
				uploadFinished();
			};

			XHR.upload.onprogress = function(e)
			{
				if (e.lengthComputable)
					settings.onProgressUpdated(File, e.loaded / e.total);
			};

			XHR.sendAsBinary(data); // Initiates sending
		}

		// Initiates reading and puts us in FR.onload on complete
		FR.readAsBinaryString(File);
	}

	var methods = {
		init: function( userOptions ) {

			// This seems to extend the XMLHttpRequest object, not totally sure exactly why/if this is needed as of now
			if( !XMLHttpRequest.prototype.sendAsBinary )
			{
				XMLHttpRequest.prototype.sendAsBinary = function(datastr)
				{
					var byteValue = function(x)
					{
						return x.charCodeAt(0) & 0xff;
					}

					var ords = Array.prototype.map.call(datastr, byteValue);
					var ui8a = new Uint8Array(ords);
					this.send(ui8a.buffer);
				}
			}


			settings = $.extend(default_settings, userOptions);

			// I think this is to prevent the browser from opening the file
			$(window)
				.off('.dropUpload')
				.on('drop.dropUpload', eventKillDefault)
				.on('dragenter.dropUpload', eventKillDefault)
				.on('dragover.dropUpload', eventKillDefault)
				.on('dragleave.dropUpload', eventKillDefault)
				;

			return this.each(function(){

				$(this)
					.on('drop.dropUpload', eventDrop)
					.on('dragover.dropUpload', eventDragOver)

					// dragenter and dragleave are inherently buggy and will cause problems with text
					.on('dragenter.dropUpload', eventDragEnter)
					.on('dragleave.dropUpload', eventDragLeave)
					;
			});
		},
		destroy: function()
		{
			$(window).off('.dropUpload');

			return this.each(function(){
				$(this).off('.dropUpload');

			});
		}
	};

	$.fn.dropUpload = function( method ) { // Basically a copy/paste from jQuery plugin authoring guide

		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.dropUpload' );
		}
	};

})( jQuery );
$(function()
{
	var queueItemTemplate = '<div class="item"><div class="caption">-</div><div class="progressBar"><div class="progress"></div></div></div>';

	var upload = $('#upload');
	var dropArea = upload.find('.dropArea');

	var queue = upload.find('.queue');
	var messages = upload.find('.messages');
	var form = upload.find('form');

	dropArea.dropUpload(
	{
		'url': form.attr('action'),
		'fileMeta': function()
		{
			return form.serializeArray();
		},
		'fileParamName': 'file',
		'fileSizeMax': null,
		'onDragEnter': function()
		{
			//dropArea.addClass('isHovering');
		},
		'onDragLeave': function()
		{
			//dropArea.removeClass('isHovering');
		},
		'onDropSuccess': function()
		{
			//dropArea.removeClass('isHovering');
		},
		'onFileCompleted': function(File)
		{
			File.QueueItem.queueRemove();
		},
		'onFileFailed': function(File, msg)
		{
			File.QueueItem.setCaption(File.name + ': ' + msg, false);
		},
		'onFileQueued': function(File)
		{
			File.QueueItem = Cordless.Interface.importQueueAdd(File.name);
		},
		'onFileSucceeded': function(File, response)
		{
			try
			{
				var json = jQuery.parseJSON(response);
				File.QueueItem.setCaption(json.data, json.status);
			}
			catch(e)
			{
				File.QueueItem.setCaption('Unknown Response from Server', false);
			}
		},
		'onProgressUpdated': function(File, progress)
		{
			File.QueueItem.setProgress(progress);
		},
		'onQueueCompleted': function()
		{
			console.log('Queue Complete');
		}
	});

	$(window)
		.on('dragenter.cordless', function(e)
		{
			// Make sure it's files
			if( e.dataTransfer.types.contains && !e.dataTransfer.types.contains("Files") ) // Firefox
				return false;

			if( e.dataTransfer.types.indexOf && e.dataTransfer.types.indexOf("Files") == -1 ) // Webkit
				return false;

			Cordless.Interface.importQueueOpen();
		})
		;
});
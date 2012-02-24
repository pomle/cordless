$(function(){

	var queueItemTemplate = '<div class="item"><div class="fileName">-</div><div class="progressBar"><div class="progress"></div></div></div>';

	$('.fileUpload').each(function(i) {
		var fileUpload = $(this);
		var fileList = fileUpload.find('.fileList');

		var form = fileUpload.closest('form');

		var messageBox = fileUpload.find('.messageBox');
		var dropbox = fileUpload.find('.dropbox');
		var destURL = fileUpload.attr('data-url');

		dropbox.dropUpload({
			'url': form.attr('action'),
			'fileMeta': function()
			{
				return form.serializeArray(); // Attach form data to each dropped file
			},
			'fileParamName': 'file',
			'fileSizeMax': 1000 * 1024 * 1024, // ONE GIGIDIBYTE
			'onFileCompleted': function(File)
			{
				File.queueItem.remove(); // Removed DOM queue item on completion
			},
			'onFileQueued': function(File) // Created DOM queue item and attaches it to the File object
			{
				var qi = $(queueItemTemplate);
				qi.find('.fileName').html(File.name);
				qi.appendTo(fileList);
				File.queueItem = qi;
			},
			'onFileSucceeded': function(File, response)
			{
				try
				{
					if( response = $.parseJSON(response) )
					{
						if( response.message )
							Messenger.display(messageBox, response.message);

						if( response.data )
							FormManager.fill(response.data, form);

						if(response.call)
							eval(response.call);
					}
				}
				catch(e)
				{
					alert("Bad Response from Server, Reason: " + e.message);
				}

			},
			'onProgressUpdated': function(File, progress)
			{
				File.queueItem.find('.progress').css('width', (progress * 100) + '%');
			},
			'onQueueCompleted': function()
			{
			}
		});
	});
});
$(function(){

	var template = '<div class="item"><div class="fileName">-</div><div class="progressBar"><div class="progress"></div></div></div>';

	$('.fileUpload').each(function(i) {
		var fileUpload = $(this);
		var fileList = fileUpload.find('.fileList');

		var form = fileUpload.parent('form');

		var messageBox = fileUpload.find('.messageBox');
		var dropbox = fileUpload.find('.dropbox');
		var destURL = fileUpload.attr('data-url');

		dropbox.filedrop({
			// The name of the $_FILES entry:
			paramname: 'file',

			maxfiles: 100,
			maxfilesize: 1000,
			url: destURL,
			data: {},

			drop: function()
			{
				this.data = form.serializeJSON();
				Messenger.clear(messageBox);
			},

			error: function(err, file)
			{
				switch(err)
				{
					case 'BrowserNotSupported':
						alert('Your browser does not support HTML5 file uploads!');
					break;

					case 'TooManyFiles':
						Messenger.display(messageBox, {error: ['TooManyFiles']});
					break;

					case 'FileTooLarge':
						Messenger.display(messageBox, {error: [file.name + ' is too large']});
					break;

					default:
						break;
				}
			},

			// Called before each upload is started
			beforeEach: function(file)
			{
				/*if(!file.type.match(/^image\//)){
					alert('Only images are allowed!');

					// Returning false will cause the
					// file to be rejected
					return false;
				}*/
			},

			uploadStarted:function(i, file, len)
			{
				var
					fileItem = $(template),
					fileName = $('.fileName', fileItem);

				fileName.html(file.name);

				fileItem.appendTo(fileList);

				$.data(file, fileItem);
			},

			progressUpdated: function(i, file, progress)
			{
				$.data(file).find('.progress').width(progress + '%');
			},

			uploadFinished: function(i, file, response)
			{
				$.data(file).remove();

				if( response.message )
					Messenger.display(messageBox, response.message);

				if( response.data )
					FormManager.fill(response.data, form);

				if(response.call)
					eval(response.call);
			},

		});
	});
});
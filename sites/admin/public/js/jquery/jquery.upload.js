jQuery.fn.upload = function(callback) {
	if(typeof(callback) != 'function') { callback = function() {}; }	

	return this.each(function() {
		var scope = $(this);

		var form = scope.closest('.scope');
		
		var swfURL			= scope.find('input[name=swfURL]').val();
		var buttonID		= scope.find('.uploadBrowse').attr('id');
		var buttonURL		= scope.find('input[name=buttonURL]').val();
		var buttonX			= scope.find('input[name=buttonX]').val();
		var buttonY			= scope.find('input[name=buttonY]').val();
		var uploadURL		= scope.find('input[name=uploadURL]').val();
		//var uploadURL		= form.attr('action');
		var fileSizeLimit	= scope.find('input[name=fileSizeLimit]').val();

		var uploadStatus = scope.find('.uploadStatus');
		var uploadStatistics = scope.find('.uploadStatistics');
		var uploadMessages = scope.find('.uploadMessages');
		var uploadQueue  = scope.find('.fileQueue');	
		var progressBar  = scope.find('.uploadProgressIndicator');	
		var progressPercentage = scope.find('.uploadProgressPercentage');
		var progressDataSize = scope.find('.uploadProgressDataSize');
		var progressSpeed = scope.find('.uploadProgressSpeed');
		var progressTime = scope.find('.uploadProgressTime');
		var progressQueue = scope.find('.uploadProgressQueue');

		var swfu = new SWFUpload({
			//debug: 1,
			upload_url	: uploadURL,
			flash_url	: swfURL,
			button_placeholder_id : buttonID,
			button_image_url: buttonURL,
			button_width: buttonX,
			button_height: buttonY,
			button_cursor : SWFUpload.CURSOR.HAND,
			file_post_name	: 'upload',
			post_params		: {},
			file_size_limit : fileSizeLimit,
			file_upload_limit : 0,
			file_queue_limit : 0,
			requeue_on_error : true,
			upload_start_handler : function(file) {
				/*uploadStartDisplay.hide();
				uploadCancelDisplay.show();
				uploadStatistics.show();*/
				
				form.find(':input').each(function() {
					var key = $(this).attr('name');
					var val = $(this).attr('value');
					swfu.removePostParam(key);
					swfu.addPostParam(key, val);
				});

				//uploadStatus.show('fast');
				//scope.find('#'+file.id).removeClass().addClass('progress');
				return true;
			},
			upload_progress_handler : function(file, bytes, bytestotal) {
				var percentage = (bytes / bytestotal) * 100;
				progressPercentage.html(Math.floor(percentage) + '%');
				progressBar.css('width', percentage + '%');

				progressDataSize.html(SWFUpload.speed.formatBytes(file.sizeUploaded) + ' / ' + SWFUpload.speed.formatBytes(file.size));
				progressSpeed.html(SWFUpload.speed.formatBytes((file.averageSpeed/10))+'/s');
				//progressTime.html(SWFUpload.speed.formatTime(file.timeRemaining));
				//if(bytes == bytestotal) { scope.find('.uploadStatus div').toggle(); }
			},
			upload_success_handler : function(file, status) {
				//alert(status);

				eval('var data = ' + status);
				
				Messenger.display(uploadMessages, data.message);

				if(data.call) eval(data.call);

				if(data.action) {
					switch(data.action) {
						case 'fail':
							scope.find('#'+file.id).removeClass().addClass('failed');
							break;
						case 'success':
							scope.find('#'+file.id).removeClass().addClass('complete');
							break;
					}
				}
			},
			upload_error_handler : function(file, code, msg) {
				//alert('Error: '+ msg + ' | ' + code);

				switch(code) {
					case '404':
						Messenger.display(uploadMessages, { error: ['Receiver file not found.'] });
						break;
					
					default:
						Messenger.display(uploadMessages, { error: ['"' + file + '": ' + msg] });
				}
				//scope.find('#'+file.id).removeClass().addClass('failed');
				//alert(code + " : " + msg); 
			},
			upload_complete_handler : function() {
				updateQueueProgress();
				/*uploadStartDisplay.show();
				uploadCancelDisplay.hide();
				uploadStatistics.hide();
				uploadStatus.hide('fast');
				uploadQueue.html('');*/
				callback(); 
			},
			file_queued_handler : function(file) {
				uploadQueue.append('<li id="'+file.id+'">'+file.name+'</li>');
				Messenger.clear(uploadMessages);
			},
			file_dialog_start_handler: function() {
				//Messenger.clear(uploadMessages);
			},
			file_dialog_complete_handler: function(filesSelected, filesQueued, queueLength) {
				updateQueueProgress();
			},
			queue_complete_handler : function() {
				uploadQueue.html('');
			},
			swfupload_loaded_handler : function() {
				/*scope.find('.fallbackUpload').hide();
				scope.find('.modernUpload').css('height', 'auto').show();*/
		
				this.addPostParam('action', 'upload');
			}
		});

		var updateQueueProgress = function() {
			var stats = swfu.getStats();
			//var filesTries = stats.successful_uploads+stats.upload_errors+stats.upload_cancelled;
			progressQueue.html(stats.files_queued);
		}

		var uploadStart = scope.find('.uploadStart').click(function(event) { 
			event.preventDefault();
			swfu.startUpload();
		});


		var uploadCancel = scope.find('.uploadCancel').click(function() { 
			swfu.cancelUpload(); return false; 
		});
		//var uploadBrowse = scope.find('.browse');
	
		/*var uploadStartDisplay = uploadStart.parent();
		var uploadCancelDisplay = uploadCancel.parent();
		var uploadBrowseDisplay = uploadBrowse.parent();*/

	});
};

$(document).ready(function() {
	$('.fileUpload').upload();
});
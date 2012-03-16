function InterfaceController(ePlayer, ePlayqueue, eImport)
{
	var
		self = this,
		timerQueueClose = null;

	var eInterface = $('body');

	this.importQueueAdd = function(caption)
	{
		clearTimeout(timerQueueClose);

		function ImportQueueItemController(caption)
		{
			var eImportQueueItem = $('<div class="item"><div class="caption"></div><div class="progressBar"><div class="progress"></div></div></div>');

			var eQueue = eImport.find('.queue');

			this.progress = 0;

			this.markComplete = function()
			{
				eImportQueueItem.addClass('success');
				return this;
			}

			this.markFailed = function()
			{
				eImportQueueItem.addClass('error');
				return this;
			}

			this.queueRemove = function()
			{
				setTimeout(this.remove, 4000);
			}

			this.setCaption = function(text, status)
			{
				eImportQueueItem.find('.caption').text(text);

				if( status === true )
					this.markComplete();
				else if( status === false )
					this.markFailed();

				return this;
			}

			this.setProgress = function(fraction)
			{
				this.progress = Math.min( Math.max(0, fraction), 1);

				eImportQueueItem.find('.progress').css('width', (this.progress * 100) + '%');
				return this;
			}

			this.remove = function()
			{
				eImportQueueItem.slideUp(
					'normal',
					function()
					{
						eImportQueueItem.remove();

						if( eQueue.find('.item').length == 0 )
							timerQueueClose = setTimeout(self.importQueueClose, 250);

					});

				return this;
			}

			this.setCaption(caption);

			self.importQueueOpen();

			eImportQueueItem.appendTo( eQueue );
		}

		var ImportQueueItem = new ImportQueueItemController(caption);

		return ImportQueueItem;
	}

	this.importQueueClose = function()
	{
		eImport.removeClass('extended');
		return this;
	}

	this.importQueueLockToggle = function()
	{
		var c = 'uploadLocked';
		eInterface.toggleClass(c);
		userSetting("WebUI_Upload_Locked", eInterface.hasClass(c) ? c : '');
		return this;
	}

	this.importQueueLock = function()
	{
		if( !eInterface.hasClass('uploadLocked') )
			this.importQueueLockToggle();

		return this;
	}

	this.importQueueOpen = function()
	{
		eImport.addClass('extended');
		return this;
	}

	this.importQueueUnlock = function()
	{
		if( eInterface.hasClass('uploadLocked') )
			this.importQueueLockToggle();

		return this;
	}

	this.playqueueLockToggle = function()
	{
		var c = 'uploadLocked';
		eInterface.toggleClass(c);
		userSetting("WebUI_PlayQueue_Locked", eInterface.hasClass(c) ? c : '');
		return this;
	}
}
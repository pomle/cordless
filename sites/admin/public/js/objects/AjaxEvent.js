var AjaxEvent = {

	invoke: function(callingElement, dUrl, sendDataSource, resultElement)
	{
		this.callingElement = $(callingElement);

		this.callingElement.addClass('ajaxEvent');

		this.parentForm  = this.callingElement.closest('form');
		this.scopeEditor = this.parentForm;

		this.sendDataSource	= $(sendDataSource ? sendDataSource : this.parentForm.find(':input').not('[readonly]'));

		this.messageElement = this.callingElement.closest('form').find('.messages,.messageBox');

		this.Antiloop = this.parentForm.prev('form.antiloop');

		this.prepareEvent();

		var sendData = AjaxEvent.sendDataSource.serialize();

		$.ajax({
			url: dUrl,
			type: 'POST',
			data: sendData,
			dataType: 'json',
			success: function(response, status, xhr)
			{
				if( !response )
				{
					alert('Response Empty');
					return false;
				}

				AjaxEvent.processMessages(response.message);
				AjaxEvent.processReturnData(response.data);

				switch(response.action)
				{
					case 'remove':
					case 'delete':
						AjaxEvent.deleteAction();

					case 'add':
					case 'save':
					case 'reload':
						AjaxEvent.saveAction();
						break;

					case 'new':
						AjaxEvent.newAction(response.data);
						break;

					case 'load':
						AjaxEvent.loadAction();
						break;
				}

				if(response.call) eval(response.call);

				AjaxEvent.postEvent();
			},
			error: function(req, status, error)
			{
				alert('Error! Please contact administrator. (Reason: ' + status + ', Error: ' + error + ')');
			},
			complete: function()
			{
				AjaxEvent.postEvent();
			}
		});
	},

	prepareEvent: function()
	{
		Messenger.clear(this.messageElement);
	},

	postEvent: function()
	{
		this.callingElement.removeClass('ajaxEvent');
	},

	newAction: function(data)
	{
		FormManager.clean(AjaxEvent.scopeEditor);
		this.processReturnData(data);
	},

	saveAction: function(data)
	{
		this.sendDataSource.find(':input').removeClass('unsaved');
		this.Antiloop.trigger('reload');
	},

	loadAction: function()
	{},

	deleteAction : function()
	{
		FormManager.clean(AjaxEvent.scopeEditor);
	},

	processMessages: function(messages)
	{
		if(messages) Messenger.display(this.messageElement, messages);
	},

	processReturnData: function(data)
	{
		if(data) FormManager.fill(data, AjaxEvent.scopeEditor);
	}
}

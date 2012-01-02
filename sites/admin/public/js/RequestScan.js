$(document).ready(function() {
	var messagesCleared = false;

	var requestIDInput = $('input[name=requestID]');
	var messageElement = $('#errors .messages');

	requestIDInput.focus();

	$('form.editor').submit(function(event) {
		event.preventDefault();

		var form = $(this);

		var requestURL	= form.attr('action');
		var requestData	= form.serialize();

		$.ajax({
			url		: requestURL,
			type	: 'POST',
			data	: requestData,
			dataType: 'json',
			success	: function(returnData) {
				
				if( returnData.message ) {
					if( !messagesCleared ) {
						Messenger.clear(messageElement);
						messagesCleared = true;
					}
					Messenger.display(messageElement, returnData.message, true);
				}

				reloadListing('#log');
			}
		});

		requestIDInput.val('');
	});
});
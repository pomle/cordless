AjaxEvent.prepareEvent = function() {
	Messenger.clear(this.messageElement);
	$('td.MESSAGE').html('');
}

AjaxEvent.processReturnData = function(data) {
	if( !data ) return true;

	$.each(data, function(index, htmlContent) {
		$('#row'+index).find('.MESSAGE').html(htmlContent);
	});
}
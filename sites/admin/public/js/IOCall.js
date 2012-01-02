$(document).ready(function() {
	var IOCall = $('form.IOCall');

	if( IOCall.length )
	{
		IOCall.submit(function(e) {
			e.preventDefault();
		});

		IOCall.find('.IOControl a.button').bind('click', function(e) {
			e.preventDefault();
			AjaxEvent.invoke(this, this.href);
		});
	}
});
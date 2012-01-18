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

	$('form.antiloop').bind('postReload', function()
	{
		var rowID = $(this).next('form.IOCall').find(':input[name$="ID"]').val();
		$(this).find('tr#id_'+rowID).addClass('active');
	});
});
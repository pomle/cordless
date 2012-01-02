$(document).ready(function() {

	var antiloop = $('form.antiloop');

	antiloop.find('.control :input').bind('keypress', function(e) {
		if(e.keyCode == 13){
			e.preventDefault();
			antiloop.trigger('reload');
		}
	});
});
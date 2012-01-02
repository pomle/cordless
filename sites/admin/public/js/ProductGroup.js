$(document).ready(function() {
	$('input.ajaxSuggest').keydown(function(event) {
		if( event.keyCode == 13 ) {
			$(this).closest('fieldset').find('a.add').click();
		}
	});
});
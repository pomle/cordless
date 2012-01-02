$(document).ready(function() {
	$('form.IOCall').find(':input').focus(function(e) {
		$(this).closest('tr').find('input[type=radio]').attr('checked', true);
	});
});
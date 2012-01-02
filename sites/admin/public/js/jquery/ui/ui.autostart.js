$(document).ready(function() {
	$.datepicker.setDefaults({
		dateFormat: 'yy-mm-dd',
		buttonImage: '/layout/calendar_edit.png',
		buttonImageOnly: true,
		showAnim: 'fadeIn',
		showOn: 'button'
	});

	$('.datepicker').datepicker();
});
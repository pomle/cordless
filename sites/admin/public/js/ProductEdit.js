$(document).ready(function() {
	$('#togglePreview').click(function(event) {
		event.preventDefault();
		$('#previewContainer').toggle();
		$('#refreshPreview').toggle().keyup();
	});

	$('#refreshPreview').click(function(event) {
		event.preventDefault();
		window.frames[0].location.reload(true);
	});

	$('#description').keyup(function() {
		var html = $(this).val();
		$('#preview').contents().find('#description').html(html);
	});

	$('.popup .form_control a.ajaxTrigger').live('click', function() {
		var selector = $(this).closest('.tab');
		selector = selector.attr('id');
		reloadListing('#'+selector);
	});

});
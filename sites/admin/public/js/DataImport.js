$(document).ready(function() {
	$("select[name='table']").change(function() {
		var selectedField = $(this).val();
		$('.fieldset').hide();
		$('#'+selectedField).show();
	});

});
$(document).ready(function() {
	
	var scope = $('#questionTypes');
	scope.find('input[name^=questionTypes]').change(function() {

		alert( $(this).val() );

		

	});

});
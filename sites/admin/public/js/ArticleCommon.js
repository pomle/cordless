$(document).ready(function() {
	var internalNameSource = $('.articleForm input[name=internalName]');
	var nameReceiver = $('.articleForm input[name=name]');

	internalNameSource.keyup(function() {
		var sourceText = $(this).val();
		
		if( nameReceiver.val().length == 0 ) {
			nameReceiver.addClass('recipient');
		}

		if( nameReceiver.hasClass('recipient') ) nameReceiver.val(sourceText);
	});

	nameReceiver.keyup(function() {
		$(this).removeClass('recipient');
	});
});
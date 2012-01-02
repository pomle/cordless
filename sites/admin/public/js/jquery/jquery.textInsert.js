jQuery.fn.textInsert = function(trigger, sendFormParams) {

	return this.each(function() {

		$(this).click(function(e) {

			e.preventDefault();

			var url = this.href;

			var params = sendFormParams ? $(this).closest('form').serialize() : '';

			if( !trigger ) trigger = this;
			
			var textArea = $(trigger).closest('.textEditor').find('textarea');
			
			var selection = textArea.selection();

			params += '&innerText=' + selection;
		
			$.ajax({
				url: url,
				type: 'post',
				data: params,
				success: function(textString) {
					textArea.insertAtCaret(textString);
				}
			});

		});
	});
};
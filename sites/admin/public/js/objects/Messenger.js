var Messenger = {

	display: function message(scopes, messages, prepend) {
		var scopes = $(scopes).eq(0);

		if(typeof(messages) == 'object') {

			$.each(messages, function(type, strings) {
				var output = scopes.find('.'+type);

				$.each(strings, function(index, text) {
					if(text) {
						if(output.length) {
							var preparedText = '<li>' + text.replace(/\n/, '<br />') + '</li>';

							if( prepend ) {
								output.prepend(preparedText);
							}else{
								output.append(preparedText);
							}

							output.show();
						}else{
							alert(text);
						}
					}
				});

			});
		}
	},

	clear: function(scopes) {
		scopes.find('.message').html('').hide();
	}
}
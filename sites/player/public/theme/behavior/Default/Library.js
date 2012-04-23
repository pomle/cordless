$(function()
{
	var Library = Cordless.Library;

	$(document)
		.on("click", '.panelLibrary', function(e)
		{
			e.preventDefault();
			var href = $(this).attr('href');

			// When href begins with # it means it is a simplified link, so autogenerate URL with placeLibrary()
			if( href.match(/^#/) )
				Library.goTo(href.substr(1));
			else
				Library.goToURL(href);
		});


	$('nav.library')
		.find('.historySkip').on("click", function(e) {
			e.preventDefault();
			var d = parseInt(this.rel, 10);
			Library.goToHistorySkip(d);
		});

	$("#library .trail").on("click", "a", function(e) {
		e.preventDefault();
		var i = parseInt(this.rel, 10);
		Library.goToHistoryIndex(i);
	});

	$('#player')
		.on("click", ".trackinfo .title", function(e) {
			e.preventDefault();
			Library.goTo("NowPlaying");
		})
		;

	$('form#search').on('submit', function(e) {
		e.preventDefault();
		var url = $(this).attr('action') + '&' + $(this).serialize();
		Library.goToURL(url);
	});


	$('#library')
		.on("click", "form button.formTrigger", function(e) {
			e.preventDefault();

			var eButton = $(this);
			var eForm = eButton.closest('form');
			var eResponse = eForm.find('.response');

			eResponse.html('');

			var data = eForm.serialize();
			data += '&' + encodeURIComponent( eButton.attr('name') ) + '=' + encodeURIComponent( eButton.attr('value') );

			$.ajax({
				'url': eForm.attr('action'),
				'type': 'POST',
				'data': data,
				'dataType': 'json',
				'success': function(response)
				{
					if( eResponse.length )
						eResponse.html(response.data);
					else
						alert(response.data);
				}
			});

		});
		;
});

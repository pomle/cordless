$(document).ready(function() {
	$('a.toggleEnabled').click(function(event) {
		event.preventDefault();

		var item = $(this);
		var url = item.attr('href');
		var image = item.find('img');

		$.ajax(
			{
				url: url,
				type: 'GET',
				dataType: 'json',
				success: function(result) {
					if( result.data.status == 'accepted' ) {
						image.attr('src', '/layout/accept_tiny.png');
					}else{
						image.attr('src', '/layout/cancel_tiny.png');
					}
				}
			}
		);		
	});

	$('a.setStatus').click(function(event) {
		event.preventDefault();

		var item = $(this);
		$.ajax({
			url: item.attr('href'),
			type: 'GET',
			dataType: 'json',
			success: function(resp) {
				if (resp.data.call) {
					eval(resp.data.call);
				}
			}
		});
	});
				
	function removeReview(id) {
		$('tr#' + id).remove();
	};
});

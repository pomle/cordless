var Populus = {

	launch: function(url, trigger, params, callback) {

		if( typeof(callback) != 'function' ) {
			callback = function() {};
		}

		var popupID = 'popup_' + uniqid();

		var trigger = $(trigger);

		var popupHolder = $('#populus');

		var offset = trigger.offset();
		var x = offset.left;
		var y = offset.top + (trigger.height() / 2);

		var url = url + '&id=' + popupID + '&x=' + x + '&y=' + y;

		//$.get(url, null, function(html) { scope.append(html); });

		$.ajax({
			type: 'GET',
			url: url,
			data: params,
			dataType: 'json',
			success: function(response)
			{
				if(response.status == 'success') {

					var closeEvent = function() {};

					var self = $(response.html).appendTo(popupHolder).data('trigger', trigger);

					eval(response.script);

					self.find('.close').click(function() {
						closeEvent();
						self.remove();
					});

				}
				else
				{
					alert(response.message);
				}
			}
		});

	}
}

$(document).ready(function() {

	$('a.popupTrigger').live('click', function(event) {
		event.preventDefault();
		trigger = $(this);
		Populus.launch(this.href, this, $(this).closest('.populusLimit').find(':input').serialize(), function(returnValue) { trigger.prev().val(returnValue); } );
	});
});
function initAjaxSuggest() {
	$('input.ajaxSuggest').each(function() {
		if( !$(this).hasClass('extended') ) {
			$(this).addClass('extended').parent().css('position', 'relative').append('<div class="ajaxSuggest"></div>');
		}
	}).unbind('keyup').keyup(function() {
		var pointer = $(this).val();
		var suggestions = $(this).parent().find('div.ajaxSuggest');

		if( pointer.length > 1 && !is_numeric(pointer) ) {
			var url = '/ajax/AjaxSuggest.php?protocol='+$(this).attr('name');
			$.post(url, {pointer: pointer}, function(html) { 
				suggestions.show().html(html); 
				var items = suggestions.find('.item');
				if( items.length == 1 ) {
					items.click();
				}
			});
		}else{
			suggestions.html('').hide();
		}
	});

	$('div.ajaxSuggest .item').live('click', function() {
		var key = $(this).find('input').val();
		var suggestions = $(this).parents('div.ajaxSuggest');
		var input = suggestions.parent().find('input.ajaxSuggest').val(key).focus();
		suggestions.html('').hide();
	}).live('mouseover', function() { 
		$(this).addClass('highlight');
	}).live('mouseout', function() {
		$(this).removeClass('highlight');
	});
}

$(document).ready(function() {
	initAjaxSuggest();
});

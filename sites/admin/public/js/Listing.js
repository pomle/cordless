function sorter(who, direction) {
	var trigger = $(who);
	var container = trigger.closest('tr');
	
	if(direction > 0) {
		var destination = container.next();
		if( !destination.hasClass('row') ) return false;
		container.insertAfter(destination);
	}else{
		var destination = container.prev();
		if( !destination.hasClass('row') ) return false;
		container.insertBefore(destination);
	}

	reShade(trigger.closest('tbody'));
}

function reloadListing(selector) {
	$(selector).find('form.listing:first').each(function() {
		var url = $(this).attr('action');
		$(this).find('.results').load(url);
	});
}

function rebindListing() {
	// For popups

	$('form.listing').unbind('submit').submit(function(event) { 
		event.preventDefault();
		AjaxEvent.invoke(this, null, this);
	});

	$('.ajaxTrigger').find('select,:input').unbind('change').change(function() {
		AjaxEvent.invoke(this, null, this);
		//$(this).closest('form').submit();
	}).keyup(function(e) {
		if(e.which == 13) {
			AjaxEvent.invoke(this, null, this);
			//$(this).closest('form').submit();
		}
		//if(e.which == 13) AjaxEvent.invoke(this, null, this);
	});
}

function reShade(list) {
	list = $(list).find('tr.row').removeClass("shade").filter(':even').addClass("shade");
}

$(document).ready(function() {
	rebindListing();

	$('.listing .skipPage').live('click', function(e) {
		var input = $(this).closest('form').find(':input[name=page]');
		var newPage = intval(input.val()) + intval(this.rel);
		input.val(newPage).change();
	});

	$('.listing .sort a.ajaxTrigger').live('click', function(event) {
		event.preventDefault();
		AjaxEvent.invoke(this, this.href);
	});

	$('tbody.results.sortable').sortable({
		containment: 'parent',
		items: 'tr.row',
		axis: 'y',
		scroll: true,
		tolerance: 'pointer',
		update: function(event, ui) {
			reShade(event.target);
			$(event.target).parents('.listing').find('.sort a.ajaxTrigger').click();
		}
	});

	$('a.moveup').live('click', function(event) { 
		event.preventDefault(); 
		sorter(this, -1);
		$(this).parents('.listing').find('.sort a.ajaxTrigger').click();
	});
	
	$('a.movedown').live('click', function(event) { 
		event.preventDefault(); 
		sorter(this, 1); 
		$(this).parents('.listing').find('.sort a.ajaxTrigger').click();
	});

	$('th.checkbox a').live('click', function(event) {
		event.preventDefault();
		$(this).closest('table').find('input[type=checkbox]').each(function(i) { 
			$(this).attr('checked', ($(this).attr('checked')) ? '' : 'checked'); 
		});
	});

	$('select[name=limit]').change(function(e) {
		if( $(this).val() == '0' ) {
			if( !confirm('Är du säker på att du vill visa alla resultat? Detta kan ta lång tid.') ) { 
				e.preventDefault();
				e.stopPropagation();
			}
		}
	});
});
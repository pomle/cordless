$(document).ready(function() {
	//$('form.editor input').live('keypress', function() { $(this).addClass('unsaved'); });

	/*$('a').live('click', function(event) {
		if((msg = $(this).attr('rel')) && !confirm(msg)) return false;

	}).filter('.trigger').live('click', function(event) {
		alert('x');
	});*/
	$(':input').not('textarea,.quickFind,.scan').keydown(function(e) {
		if( e.keyCode == 13 ) {
			e.preventDefault();
		}
	});

	$('.redirect').find(':input').change(function() {
		window.location = $(this).val();
	});

	$('td.load a.ajaxTrigger').live('click', function(event) {
		event.preventDefault();
		//var url = $(this).closest('.scope').find('form.editor').attr('action');
		var sendDataSource = $(this).closest('table');
		AjaxEvent.invoke(this, this.href);
	});

	$('a.sortSave').live('click', function(event) {
		event.preventDefault();
		AjaxEvent.invoke(this, this.href, $(this).closest('table').find(':input'));
	});

	$('li.tools a.ajaxTrigger').live('click', function(event) {
		event.preventDefault();
		AjaxEvent.invoke(this, this.href);
	});

	$('a.popupTrigger').live('click', function(event) {
		event.preventDefault();
		trigger = $(this);
		Popup.launch(this.href, this, $(this).closest('.scope').find(':input').serialize(), function(returnValue) { trigger.prev().val(returnValue); } );
	});

	/*$('a').not('.trigger, .toggle, .help').click(function() {
		if($('a.save').length && $('input.unsaved').length) { return confirm("Du har gjort ändringar som inte är sparade. Är du säker på att du vill avbryta?"); }
	});*/

	$('a.setData').live('click', function(e) {
		e.preventDefault();

		var url = this.href;
		var form = $(this).closest('form');

		if( !setDataCache[url] ) {
			$.getJSON(url, function(JSONdata) {
				setDataCache[url] = JSONdata;
				FormManager.fill(JSONdata, form);
				form.submit();
			});
		}else{
			FormManager.fill(setDataCache[url], form);
			form.submit();
		}


	});


	$('a.formTrigger').live('click', function(event) {
		event.preventDefault();
		if((msg = this.rel) && !confirm(msg)) return false;
		AjaxEvent.invoke(this, this.href);
	});

	$('li.button a.formSubmitter').live('click', function(event) {
		event.preventDefault();
		$(this).closest('form').submit();
	});

	$('fieldset legend a.expand').live('click', function(event) {
		$(this).closest('fieldset').removeClass('contracted').addClass('expanded');
		return false;
	});

	$('fieldset legend a.contract').live('click', function(event) {
		$(this).closest('fieldset').removeClass('expanded').addClass('contracted');
		return false;
	});



	//$('a.trigger').live('click', function(event) { event.preventDefault(); trigger(this); });

	$('ul.tabs li').live('click', function() {
		var identifier = $(this).find('a').attr('href');
		var a = $(this);
		var b = a.addClass('selected').siblings();
		var c = b.removeClass('selected').eq(0).closest('div.tabs');
		var d = c.find('div.tab:visible:first').hide();
		$(identifier).show();
		return false;
	});

	$('ul.tabs').each(function() {
		$(this).children('li:first').addClass('selected').click();
	});

	/*$('a.addElement').live('click', function() { cElement.add(this); return false; });
	$('a.removeElement').live('click', function() { cElement.remove(this); return false; });*/

	$('a.help').click(function(e) { $($(this).attr('href')).css({left: (e.pageX + 10), top: (e.pageY)}).toggle(); return false; });
});



/*var cElement = {
	add: function(who) {
		var playground = $(who).parents('.dynamic');
		var sourceElement = playground.find('.template');
		var newElement = sourceElement.clone().removeClass('template');
		sourceElement.after(newElement);

	},
	remove: function(who) {
		$(who).parents('tr:first, li:first').remove();
	},
	clear: function(who) {
		var playground = $(who).parents('.dynamic');
		playground.find('.template').siblings().remove();
	}
}*/

function hasChanged() { return $(':input').hasClass('changed'); }

function strrpos(haystack, needle, offset) {
    var i = haystack.lastIndexOf( needle, offset ); // returns -1
    return i >= 0 ? i : false;
}

function intval(value) {
	var number = parseInt(value);
	if(isFinite(number)) { return number; } else { return 0; }
}

function floatval(value) {
	var number = parseFloat(value);
	if(isFinite(number)) { return number; } else { return 0; }
}

function getParams(url) {
	return url.substr(url.indexOf('?')+1);
}

function uniqid() {
	var uniqid = Math.floor(Math.random()*999999);
	return uniqid;
}

function is_numeric (mixed_var) {
    if (mixed_var === '') {
        return false;
    }

    return !isNaN(mixed_var * 1);
}

var saveable = false;
var simpleform = false;
var setDataCache = [];

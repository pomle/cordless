var FormManager = {
	fill: function(data, form) {
		var playground = (form == null) ? $(document) : $(form) ;
		var box;

		$.each(data, function(name, value) {

			if(value == null) { value = ''; }

			if(typeof(value) == 'object') {

				FormManager.fill(value);

			}else{

				var idField = $('#'+name);
				if( idField.length ) idField.html(value);

				box = playground.find('[name=' + name + ']');

				if( box.length == 0 ) {
					box = playground.find('[name^=' + name + ']');
				}

				if( box.length ) {
					FormManager.setfield(box, value);
				}
			}
		});
	},
	setfield: function(object, value) {
		var object = $(object).filter('[readonly!=readonly]');
		if(object.hasClass('image')) {
			object.css('background-image', 'url('+value+')');
		}else{
			switch(object.attr('type')) {
				case 'text':
				case 'hidden':
					object.val(value);
					break;

				case 'checkbox':
					object.each(function(index, element) {
						var x = $(element).attr('value');
						var isChecked = (x & value || x == value);
						$(element).attr('checked', isChecked);
					});
					break;

				case 'radio':
					// Reset all
					object.attr('checked', false)

					// Pick out the one with matching value
					object = object.filter('[value=' + value + ']');

					// Check it
					object.attr('checked', true);
					break;

				case 'button':
					break;

				default:
					object.val(value);
					break;
			}
			object.removeClass('unsaved').change();
		}
	},
	clean: function(form) {
		var fields = $(form).find(':input').add('.image').filter('[readonly!=readonly]');
		fields.each(function(i) {
			FormManager.setfield(this, '');
			//$('#'+$(this).attr('name')).html('');
		});

		var items = $(form).find('.itemlist ul li').not('.template').remove();
	}
}
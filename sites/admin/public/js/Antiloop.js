var Antiloop =
{
	reload: function(element, url)
	{
		var antiloop = $(element);

		antiloop.trigger('preReload');
		antiloop.find('thead, tbody').addClass('loading');

		$.ajax({
			url: url,
			type: 'get',
			dataType: 'html',
			complete: function()
			{
				antiloop.trigger('postLoad').trigger('postReload');
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				alert(textStatus);
			},
			success: function(html)
			{
				antiloop.find('.content').html(html);
			}
		});
	}
}


$(document).ready(function()
{
	var antiloops = $('form.antiloop');

	antiloops.each(function() {

		var antiloop = $(this);

		antiloop.submit(function(e) {
			e.preventDefault();
			antiloop.trigger('reload');
		});

		antiloop.bind('reload', function() {
			var url = antiloop[0].action;
			url += antiloop.serialize();
			Antiloop.reload(antiloop, url);
		});

		antiloop.find('a.pD').live('click', function(e) {
			e.preventDefault();
		});

		antiloop.find('.sort').live('click', function() {
			var parent = $(this).closest('.antiloop');
			Antiloop.reload(parent, this.href);
		});

		antiloop.find('.reload').live('click', function() {
			antiloop.trigger('reload');
		});

		antiloop.find('.prevPage,.nextPage').live('click', function() {
			var p = antiloop.find('.page');
			p.val(parseInt(p.val()) + parseInt(this.rel)).change();
		});

		antiloop.find('.filter .clear').click(function() {
			var inputs = $(this).closest('.filter').find(':input');
			inputs.each(function(i) {
				var input = $(this);

				if( input.hasClass('text') )
					input.val('').change();

				if( input.hasClass('page') )
					input.val('1').change();
			});
		});

		antiloop.find('.limit').change(function() {
			if(this.value == '0') return confirm('Really?');
			antiloop.find('.page').val(1);
		});

		antiloop.find('.page').change(function() {
			var p = parseInt(this.value);
			if( !isFinite(p) || p < 1 ) this.value = 1;
		});

		antiloop.find('.search').change(function() {
			antiloop.find('.page').val(1);
		});

		antiloop.find('.control :input').bind('change', function() {
			antiloop.trigger('reload');
		});

		antiloop.find('.trigger').bind('click', function() {
			var url = this.href;
			url += antiloop.find('tbody :input').serialize();

			var msgBoxEle = antiloop.find('.messageBox');

			$.ajax
			({
				async: false,
				url: url,
				dataType: 'json',
				success: function(response, textStatus, jqXHR)
				{
					if( response.message ) MessageBox.displayObject(msgBoxEle, response.message);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert(textStatus);
				}
			});
		});

		var form = antiloop.next('form.IOCall');

		antiloop.find('.ajaxLoad').live('click', function() {
			var url = this.href;

			var row = $(this).closest('.row');
			row.siblings().removeClass('active');

			row.addClass('loading').removeClass('failed');

			form = antiloop.next('form');

			$.ajax
			({
				async: false,
				url: url,
				dataType: 'json',
				success: function(response, textStatus, jqXHR)
				{
					if( response.data ) FormManager.fill(response.data, form);
				},
				error: function(jqXHR, textStatus, errorThrown)
				{
					alert(textStatus);
					row.addClass('failed');
				},
				complete: function(jqXHR, textStatus)
				{
					row.removeClass('loading');
				}
			});

		})

		form.find(':input[name$="ID"]').eq(0).change(function() {
			antiloop.find('tr.row').removeClass('active').filter('#id_' + this.value).addClass('active');
		});

		antiloop.find('.head .invertCheck').live('click', function()
		{
			var th = $(this).closest('th');
			var colIndex = th.closest('thead').find('th').index(th);

			var td = $(this).closest('table').find('td:nth-child(' + (colIndex + 1) + ')')
			var cbs = td.find('input[type=checkbox]');
			cbs.each(function() {
				$(this).attr(
					'checked',
					$(this).is(':checked') ? false : true
				);
			});
		});
	});

	antiloops.trigger('postLoad');
});
$(document).ready(function() {

	$('.tabs .tabIndex a').live('click', function(e) {
		e.preventDefault();
		var id = $(this).attr('href');
		
		$(this).addClass('active').closest('li').siblings().find('a').removeClass('active');

		//$(this).closest('.tabs').find('.tab').removeClass('visible');
		var a = $(this).closest('.tabs').children().not('.tabIndex');
		a.each(function() {
			if( $(this).is('.tab') )
				$(this).removeClass('visible');
			else
				$(this).find('.tab:first').removeClass('visible');
		});
		$(id).addClass('visible');
	})
		
	$('.tabs .tabIndex').each(function() { $(this).find('a').eq(0).click(); });
});
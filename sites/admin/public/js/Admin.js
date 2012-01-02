$(document).ready(function() {
	$('a.button').bind('click', function(e) {
		if( this.rel && !confirm(this.rel))
		{
			e.stopImmediatePropagation();
			e.preventDefault();
		}
	});

	$(':input').filter('input[type=text],input[type=password],textarea').keyup(function() {
		Cranium.paintLen(this);
	}).blur(function() {
		Cranium.hide();
	});
});

var Cranium = {
	paintLen: function(element)
	{
		var i = $(element);
		var c = $('#cranium').show();
		var iO = i.offset();
		var iH = i.outerHeight();
		iO.top += iH;
		iO.left = parseInt(iO.left);
		c.offset(iO).text(i.val().length);
	},
	hide: function()
	{
		$('#cranium').hide();
	}
};

function uniqid() {
	var uniqid = Math.floor(Math.random()*999999);
	return uniqid;
}
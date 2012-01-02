$(document).ready(function() {

	$('a.textInsert').textInsert();

	$('.preformatted').keypress(function(e) {
		//console.log(e.keyCode);
		if( e.keyCode == 9 ) {
			e.preventDefault();
			$(this).insertAtCaret("\t");
		}
	});
})

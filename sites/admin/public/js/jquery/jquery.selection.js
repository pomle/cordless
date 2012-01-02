jQuery.fn.selection = function(textString) {

	var textArea = this.get(0);

	var selStart = 0;
	var selEnd = 0;
	var selLen = 0;

	var br = ((textArea.selectionStart || textArea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false ) );

	if (br == "ie") { 
		textArea.focus();
		var range = document.selection.createRange();
		range.moveStart('character', -textArea.value.length);
		strPos = range.textString.length;

	}else if (br == "ff") {
		selStart = textArea.selectionStart;
		selEnd = textArea.selectionEnd;
	}

	var text = textArea.value;

	var selection = text.substring(selStart, selEnd);

	return selection;
};
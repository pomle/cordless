jQuery.fn.insertAtCaret = function(textString) {

	return this.each(function() {
		
		var textArea = this;//document.getElementById(areaId);

		var scrollPos = textArea.scrollTop;

		var selStart = 0;
		var selEnd = 0;

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

		selLen = (selEnd - selStart);
		
		var front = (textArea.value).substring(0, selStart);  
		var back = (textArea.value).substring(selEnd,textArea.value.length); 

		textArea.value = front + textString + back;

		selEnd = selStart + textString.length;

		if (selLen == 0 ) {
			selStart = selEnd;
		}

		if (br == "ie") { 
			textArea.focus();
			var range = document.selection.createRange();
			range.moveStart ('character', -textArea.value.length);
			range.moveStart ('character', strPos);
			range.moveEnd ('character', 0);
			range.select();

		}else if (br == "ff") {

			textArea.selectionStart = selStart;
			textArea.selectionEnd = selEnd;
			textArea.focus();
		}

		textArea.scrollTop = scrollPos;
		
	});
};
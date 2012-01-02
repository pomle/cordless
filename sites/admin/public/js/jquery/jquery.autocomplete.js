/**
*    Json key/value autocomplete for jQuery 
*    Provides a transparent way to have key/value autocomplete
*    Copyright (C) 2008 Ziadin Givan www.CodeAssembly.com  
*
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU Lesser General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU Lesser General Public License
*    along with this program.  If not, see http://www.gnu.org/licenses/
*    
*    Examples 
*	 $("input#example").autocomplete("autocomplete.php");//using default parameters
*	 $("input#example").autocomplete("autocomplete.php",{minChars:3,timeout:3000,validSelection:false,parameters:{'myparam':'myvalue'},before : function(input,text) {},after : function(input,text) {}});
*    minChars = Minimum characters the input must have for the ajax request to be made
*	 timeOut = Number of miliseconds passed after user entered text to make the ajax request   
*    validSelection = If set to true then will invalidate (set to empty) the value field if the text is not selected (or modified) from the list of items.
*    parameters = Custom parameters to be passed
*    after, before = a function that will be caled before/after the ajax request
*/
jQuery.fn.autocomplete = function(url, settings ) 
{
	return this.each( function()//do it for each matched element
	{
		//this is the original input
		var textInput = $(this);
		var scope = $(this).parents('div.itemlist');
		var autoComplete = scope.find('ul.autocomplete').css({ position: 'absolute', top: textInput.offset().top + textInput.outerHeight(), left: textInput.offset().left, width: textInput.width()});
		var typingTimeout;
		var size = 0;
		var selected = 0;

		settings = jQuery.extend(//provide default settings
		{
			minChars : 1,
			timeout: 500,
			validSelection : true,
			parameters : {}
		} , settings);

		function getData(text) {
			window.clearInterval(typingTimeout);
			if(settings.minChars == null || text.length >= settings.minChars) {

				settings.parameters.text = text;
				$.getJSON(url, settings.parameters, function(data) {
					var items = '';
					if(data.result) {
						data = data.result;
						size = data.length;
						$.each(data, function(key, value) { 
							items += '<li value="' + key + '">' + value.replace(new RegExp("(" + text + ")","i"),"<b>$1</b>") + '</li>';
						});
					}
					autoComplete.html(items).show();
					autoComplete.find('li').mousedown(function() {
						textInput.val($(this).text());
						clear();
					
					});
				});
			}
		}
		
		function clear() {
			autoComplete.html('').hide();
			size = 0;
			selected = 0;
		}	
		
		textInput.keydown(function(e) {
			window.clearInterval(typingTimeout);
			
			switch(e.which) {
				case 13:
				case 16:
				case 17:
				case 18:
					return false;

				case 40: 
				case 9:
				  selected = selected >= size - 1 ? 0 : selected + 1; break;
				case 38:
				  selected = selected <= 0 ? size - 1 : selected - 1; break;
			}

			typingTimeout = window.setTimeout(function() { getData(textInput.val()) },settings.timeout);

		}).blur(function() { 
			autoComplete.hide(); 
		}).focus(function() { 
			autoComplete.show(); 
		});
	});
};


var MessageBox =
{
	clearBox: function(msgBoxEle)
	{
		var msgEles = $(msgBoxEle).find('.message');
		MessageBox.clearElement(msgEles);
	},

	clearElement: function(msgEle)
	{
		$(msgEle).html('').removeClass('hasMessage');
	},

	displayObject: function(msgBoxEle, msgObj, append)
	{
		if( typeof(msgObj) != 'object') return false;

		if( !append ) MessageBox.clearBox(msgBoxEle);

		$.each(msgObj, function(msgType, strings)
		{
			var msgEle = $(msgBoxEle).find('.' + msgType);

			if(strings.length) msgEle.addClass('hasMessage');

			$.each(strings, function(index, string)
			{
				if( msgEle.length )
				{
					msgEle.append(string.replace(/\n/, '<br />')+'<br />')
				}
				else
				{
					alert(text);
				}
			});
		});
	}
};
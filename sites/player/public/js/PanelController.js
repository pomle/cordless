function PanelController(type, canvas, trail)
{
	var self = this;

	var historyPointer = 0;
	var historySize = 100;
	var history = [];

	var last_url;


	this.goTo = function(name, params)
	{
		return this.goToURL('./ajax/Panel.php?type=Library&name=' + name, params);
	}

	this.goToHistoryIndex = function(index)
	{
		if( !history[index] ) return false;

		canvas.html(history[index].content);
		historyPointer = index;
		updateTrail();

		//console.log('History Pointer: ' + historyPointer);

		return true;
	}

	this.goToHistoryLast = function()
	{
		return this.goToHistoryIndex(history.length - 1);
	}

	this.goToHistoryNext = function()
	{
		return this.goToHistorySkip(1);
	}

	this.goToHistoryPrev = function()
	{
		return this.goToHistorySkip(-1);
	}

	this.goToHistorySkip = function(d)
	{
		return this.goToHistoryIndex(historyPointer + d);
	}

	this.goToURL = function(url, params)
	{
		if( canvas.hasClass('isLocked') )
		{
			console.log('Destination Panel Locked');
			return false;
		}

		if( params )
		{
			$.each(params, function(key, value) {
				url += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(value);
			});
		}

		if( history[historyPointer].url == url )
		{
			console.log("Identical URL");
			return false;
		}

		$.ajax(
		{
			'url': url,
			'type': 'get',
			'complete': function(jqXHR, textStatus)
			{},
			'error': function(jqXHR, textStatus, errorThrown)
			{
				alert(textStatus);
			},
			'success': function(response, textStatus, jqXHR)
			{
				historyAdd($(response), url);
				self.goToHistoryLast();
			}
		});

		return true;
	}

	var historyAdd = function(jQueryContent, source_url) // The neat thing about putting this in a jQuery object is that any updates in the dom will be "duplicated" in the cache, since it's a reference instead of a copy
	{
		history = history.slice(0, historyPointer + 1); /// When new history arrives, remove history that comes after current index

		history.push(
		{
			'title': jQueryContent.filter('.header').eq(0).data('title'),
			'content': jQueryContent,
			'url': source_url
		});

		if( history.length > historySize ) history.shift(); // Drop first added item when history grows too large
	}

	var updateTrail = function()
	{
		var
			realIndexStart = 0,
			realIndexEnd = history.length - 1,
			indexStart = Math.max(historyPointer - 5, realIndexStart),
			indexEnd = Math.min(historyPointer + 5, realIndexEnd);

		trail.html('');

		if( indexStart > realIndexStart ) trail.append(' &raquo; ');

		for(var i = indexStart; i <= indexEnd; i++)
		{
			trail.append('<a href="#" rel="' + i + '" class="' + (historyPointer == i ? 'isCurrent' : '') + '">' + history[i].title + '</a>');

			if( i < realIndexEnd ) trail.append(' &raquo; ');
		}
	}

	historyAdd($(canvas[0].innerHTML));
	updateTrail();
}
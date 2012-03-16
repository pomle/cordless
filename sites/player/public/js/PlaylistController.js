function PlaylistController(ePlaylist)
{
	var self = this;

	var eItems = ePlaylist.find('.userTracks');

	this.afterCurrent = function(userTrackItems)
	{
		var cur = self.itemCurrent();

		if( cur.length )
			return cur.after(userTrackItems);

		return self.prependTo(userTrackItems);
	}

	this.appendTo = function(userTrackItems)
	{
		eItems.append(userTrackItems);
		return this;
	}

	this.clear = function()
	{
		eItems.html('');
		return this;
	}

	this.getItems = function()
	{
		return eItems.children('.userTrack');
	}

	this.replaceWith = function(userTrackItems)
	{
		eItems.html(userTrackItems);
		return this;
	}

	this.itemCurrent = function()
	{
		var currentIndex, items, item;

		items = self.getItems();

		return items.filter('.isCurrent'); // Return items with class isCurrent. There should always just be one
	}

	this.itemNext = function()
	{
		return self.itemSkip(1);
	}

	this.itemPrev = function()
	{
		return self.itemSkip(-1);
	}

	this.itemSeek = function(index)
	{
		var items = self.getItems();

		if( index < 0 || index >= items.length ) return false;

		return items.eq(index);
	}

	this.itemSkip = function(diff)
	{
		var
			currentIndex,
			newIndex,
			item;

		item = self.itemCurrent();

		currentIndex = item.index();

		// If there is no currentIndex, go to first index
		newIndex = ( currentIndex == -1 ) ? 0 : currentIndex + diff;

		return self.itemSeek(newIndex);
	}

	this.prependTo = function(userTrackItems)
	{
		eItems.prepend(userTrackItems);
		return this;
	}

	this.shuffle = function()
	{
		var item_current = this.itemCurrent().detach(); // Remove currently playing from set
		var items = this.getItems();

		for(
			var j, x, i = items.length; i;
			j = parseInt(Math.random() * i),
			x = items[--i], items[i] = items[j], items[j] = x
		);

		self.replaceWith(item_current).appendTo(items);

		return this;
	}
}
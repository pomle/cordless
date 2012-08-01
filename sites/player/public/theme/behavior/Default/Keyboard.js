$(function()
{
	var lockKeyboard = false;

	var
		Player = Cordless.Player,
		Library = Cordless.Library;

	$(document).keydown(function(e)
	{
		if( lockKeyboard ) return true;

		switch(e.keyCode)
		{
			case 37: // Left Arrow
			case 177: // MM Prev
				e.preventDefault();
				Player.playlistPrev();
			break;

			case 39: // Right Arrow
			case 176: // MM Next
				e.preventDefault();
				Player.playlistNext();
			break;

			case 34: // Page Down
			break;

			case 33: // Page Up
			break;

			case 36: // Home
			break;

			case 35: // End
			break;

			case 80: // P
				playqueueLockToggle();
			break;

			case 81: // Q
				e.preventDefault();
				Library.goToHistoryPrev();
			break;

			case 87: // W
				e.preventDefault();
				Library.goToHistoryNext();
			break;

			case 85: // U
				uploadLockToggle();
			break;

			case 32: // Spacebar
			case 179: // MM Play/Pause
				e.preventDefault();
				Player.playbackToggle();
			break;
		}
		console.log(e.keyCode);
	})
	.on("focus", ":input", function() { lockKeyboard = true; })
	.on("blur", ":input", function() { lockKeyboard = false; })
	;
	/*.find(':input')
		.on("focus", function() { lockKeyboard = true; })
		.on("blur", function() { lockKeyboard = false; })
		;*/
});

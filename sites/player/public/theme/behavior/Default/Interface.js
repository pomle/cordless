$(function()
{
	// PlayQueue follows window-height
	//var sidebar = $('.sidebar');
	var sPlayQueue = $('#playqueue').find('.userTracks');
	var sUpload = $('#playqueue').find('.queue');

	$(window).on('resize', function()
	{
		var winHeight = $(this).height();
		sPlayQueue.css('height', (winHeight - 130) + 'px');
		sUpload.css('max-height', (winHeight - 130) + 'px');
	})
	.trigger('resize');

	$('#upload .lock').on('click', function(e)
	{
		e.preventDefault();
		uploadLockToggle();
	});

	$('#upload .close').on('click', function(e)
	{
		e.preventDefault();
		Cordless.Interface.importQueueUnlock();
		Cordless.Interface.importQueueClose();
	});

	$('#playqueue .lock').on('click', function(e)
	{
		e.preventDefault();
		playqueueLockToggle();
	});
});
var LastFM =
{
	'updateHomeStatus': function()
	{
		var lastfm_status = $('#last_fm_status');
		var lastfm_username = lastfm_status.data('lastfm-username');

		if( lastfm_username )
		{
			$.ajax(
			{
				type: 'GET',
				url: Cordless.LAST_FM_API_URL + '&method=user.getinfo&user=' + encodeURIComponent(lastfm_username),
				dataType: 'xml',
				success: function(xml)
				{
					var xml = $(xml);
					var stats = {};

					stats['imageURL'] = xml.find('image[size="small"]').text();
					stats['playcount'] = xml.find('playcount').text();
					stats['pageURL'] = xml.find('url').text();

					lastfm_status.find('.playcount').append(stats['playcount']);

					lastfm_status.attr('data-lastfm-username', '');
				}
			});
		}
	}
}
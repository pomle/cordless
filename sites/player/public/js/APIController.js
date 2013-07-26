function APIController(api_endpoint_url)
{
	var self = this;

	api_endpoint_url += '?method=';

	var
		queue = [],
		queueSim = 0,
		queueSimMax = 1;

	this.addCall = function(method, params, callback)
	{
		queue.push({'method': method, 'params': params, 'callback': callback});
		runQueue();
		return true;
	}

	this.makeCall = function(method, params, successCallback, completeCallback)
	{
		if (!params) {
			params = {};
		}

		$.ajax({
			'type': 'POST',
			'url': api_endpoint_url + method,
			'data': 'params=' + encodeURIComponent( JSON.stringify(params) ),
			'dataType': 'json',
			'error': function(jqXHR, textStatus, errorThrows) { console.log(textStatus); },
			'success': function(response) {
				if (successCallback) {
					successCallback(response.data, response.status);
				}
			},
			'complete': completeCallback || null
		});

		return true;
	}

	var runQueue = function()
	{
		if( queueSim < queueSimMax && queue.length > 0 )
		{
			queueSim++;
			var Call = queue.shift();

			self.makeCall
			(
				Call.method,
				Call.params,
				Call.callback,
				function()
				{
					queueSim--;
					runQueue();
				}
			);
		}
	}
}
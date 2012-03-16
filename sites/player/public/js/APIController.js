function APIController()
{
	var self = this;

	var cordless_api_url = '/api/?method=';

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
		$.ajax({
			'type': 'POST',
			'url': cordless_api_url + method,
			'data': params,
			'dataType': 'json',
			'error': function(jqXHR, textStatus, errorThrows) { console.log(textStatus); },
			'success': successCallback || null,
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
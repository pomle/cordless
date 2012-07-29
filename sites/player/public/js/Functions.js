function formatDuration(seconds)
{
	var minutes = Math.floor(seconds / 60);
	seconds = Math.floor(seconds % 60);

	return minutes + ':' + zeroPad(seconds, 2);
}

function registerPlay(Track)
{
	Cordless.API.addCall('UserTrack.RegisterPlay', Track);
}

function playqueueLockToggle()
{
	var
		c = 'playqueueLocked',
		b = $('body');
	b.toggleClass(c);
	userSetting("WebUI_PlayQueue_Locked", b.hasClass(c) ? c : '');
}

function uploadLockToggle()
{
	var
		c = 'uploadLocked',
		b = $('body');
	b.toggleClass(c);
	userSetting("WebUI_Upload_Locked", b.hasClass(c) ? c : '');
}

function userSetting(key, value)
{
	var s = {}
	s[key] = value;
	Cordless.API.addCall("User.Setting" + (value == undefined ? 'Get' : 'Set'), s);
}

function zeroPad(str, len)
{
	str = '' + str;
	while(str.length < len)
		str = '0' + str;

	return str;
}
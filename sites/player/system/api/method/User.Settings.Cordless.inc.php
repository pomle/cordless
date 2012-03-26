<?
namespace Cordless;

$keepSession = true;

function APIMethod($User, $params)
{
	$settings = array(
		'Stream_Download_Format',
		'Stream_Play_Format',
		'WebUI_Global_Background_URL',
		'WebUI_Global_Background_isLocked'
	);

	foreach($settings as $setting)
		$User->setSetting($setting, isset($params->$setting) ? $params->$setting : null);

	return _("User Settings Saved");
}
<?
namespace Cordless;

function APIMethod($User, $settings)
{
	foreach($settings as $key => &$value)
		$value = $User->getSetting($key);

	return $settings;
}
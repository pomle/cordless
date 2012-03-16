<?
namespace Cordless;

$keepSession = true;

function APIMethod($User, $settings)
{
	foreach($settings as $key => &$value)
	{
		if( $User->setSetting($key, $value) )
			$value = $User->getSetting($key, $value);
	}

	return $settings;
}
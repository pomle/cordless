<?
namespace Cordless;

function APIMethod($User, $params)
{
	if( isset($params['sleep']) )
		sleep(min((int)$params['sleep'], 10));

	return $params;
}
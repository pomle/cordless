<?
namespace Cordless;

function APIMethod($User, $params)
{
	if( !DEBUG ) throw New APIException("Application not in Debug Mode");

	print_r($User);
}
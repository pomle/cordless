<?
try
{
	ensurePolicies(ACCESS_POLICY);
}
catch(Exception $e)
{
	if( defined('IS_AJAX_REQUEST') && IS_AJAX_REQUEST )
		Message::asJSON('error');
	else
	{
		header('HTTP/1.1 403 Forbidden');
		\Element\Page::error($e->getMessage(), _('Access Denied'));
		die(1);
	}
}
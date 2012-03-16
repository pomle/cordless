<?
if( defined('IS_AJAX_REQUEST') )
{
	Message::addError(_('Sessionsdata förlorad! Var vänliga logga in igen.').' <a href="/">'._('Gå till inloggning').' &raquo;</a>');
	if( defined('IS_JSON_RESPONSE') )
		Message::asJSON(false);
	else
		Message::displayElements();
}
else
{
	$userCount = \Asenine\User\Manager::getCount();

	if( $userCount === 0 )
		require DIR_ADMIN_ELEMENT . 'CreateAdmin.inc.php';
	else
		require DIR_ADMIN_ELEMENT . 'Login.inc.php';
}

die();
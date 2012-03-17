<?
function ensureLoggedIn()
{
	global $User;

	if( !$User instanceof \Asenine\User || !$User->isLoggedIn() )
		throw New Exception(_('Not logged in'));
}

function ensureAnyPolicy()
{
	ensureLoggedIn();

	global $User;

	$policies = func_get_args();
	if( !$User->hasAnyPolicy($policies) )
		throw New Exception(_('Otillräckliga rättigheter').' (' . _('Någon krävs') . ': ' . join(', ', $policies).')');
}

function ensurePolicies()
{
	ensureLoggedIn();

	global $User;

	$policies = func_get_args();
	if( !$User->hasPolicies($policies) )
		throw New Exception(_('Otillräckliga rättigheter').' (' . _('Samtliga krävs') . ': ' . join(', ', $policies).')');
}
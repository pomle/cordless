<?
switch($action)
{
	case 'flush':
		$User->settings = array();
		Message::addNotice(_('Inställningar nollställda'));
		break;

	case 'forget':
		$User->preferences = array();
		#\Manager\User::resetPreferences(USER_ID);
		Message::addNotice(_('Visningsinställningar nollställda'));
		break;

	case 'save':
		if( !isset($_POST['settings']) || !is_array($_POST['settings']) ) throw New Exception('Settings Array Missing');

		foreach($_POST['settings'] as $key => $value)
			$User->setSetting($key, $value);

		Message::addNotice(_('Inställningar sparade'));

	case 'load':
		$result = $User->settings;
		break;
}
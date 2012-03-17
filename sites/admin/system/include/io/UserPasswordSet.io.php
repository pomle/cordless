<?
class UserPasswordSetIO extends AjaxIO
{
	public function save()
	{
		global $User;

		$this->importArgs('passwordCurrent', 'passwordNew', 'passwordNewVerify');

		if( strlen($this->passwordNew) < \User::PASSWORD_MIN_LEN)
			throw New Exception(sprintf(_('Nytt lösenord för kort. Lösenord måste bestå av minst %u tecken.'), \User::PASSWORD_MIN_LEN));

		if( $this->passwordNew !== $this->passwordNewVerify )
			throw New Exception(_('Nya lösenorden matchar inte varandra'));

		if( !\Manager\User::setPassword(USER_ID, $this->passwordNew, $passwordCurrent) )
			throw New Exception(_('Nuvarande lösenord felaktigt'));

		Message::addNotice(_('Lösenord uppdaterat'));
	}
}
$AjaxIO = new UserPasswordSetIO($action);

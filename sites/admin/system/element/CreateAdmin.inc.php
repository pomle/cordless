<?
if( isset($_POST['register']) )
{
	try
	{
		\DB::autocommit(false);

		$username = $_POST['username'];
		$password = $_POST['password'];
		$passwordVerify = $_POST['passwordVerify'];

		\Operation\User::verifyUsername($username);
		\Operation\User::verifyPassword($password, $passwordVerify);


		$userID = \Manager\User::addToDB();


		$query = \DB::prepareQuery("UPDATE
				Users
			SET
				isEnabled = 1,
				isAdministrator = 1,
				username = %s
			WHERE
				ID = %u",
			$username,
			$userID);

		\DB::queryAndCountAffected($query);


		if( !\Manager\User::setPassword($userID, $password) )
			throw New Exception(_('Lösenord kunde inte sättas'));


		if( !$User = \User::login($username, $password) )
			throw New Exception(_('Användare kunde inte loggas in'));

		\DB::commit();

		header('Location: /');

		exit();
	}
	catch(Exception $e)
	{
		\DB::rollback();

		$MB = \Element\MessageBox::alert($e->getMessage());
	}
}
else
{
	$MB = \Element\MessageBox::notice(_('Det finns inga användare i databasen. Du ska nu skapa en administrator-användare. Denna användare har fullständiga rättigheter att göra ändringar i systemet. Du kan välja vilket användarnamn du vill.'));
}

$title = _('Installation');

require DIR_ADMIN_ELEMENT . 'Header.NotLoggedIn.inc.php';

echo $MB;
?>
<form action="<? echo getenv('REQUEST_URI'); ?>" method="post">
	<table>
		<tr>
			<td><? echo _('Användarnamn'); ?></td>
			<td><? echo \Element\Input::text('username', $_COOKIE['username']); ?></td>
		</tr>
		<tr>
			<td><? echo _('Lösenord'); ?></td>
			<td><? echo \Element\Input::password('password'); ?></td>
		</tr>
		<tr>
			<td><? echo _('Lösenord (verifiera)'); ?></td>
			<td><? echo \Element\Input::password('passwordVerify'); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="control">
				<input type="submit" name="register" value="<? echo _('Registrera'); ?>" />
			</td>
		</tr>
	</table>
</form>
<?
require DIR_ADMIN_ELEMENT . 'Footer.NotLoggedIn.inc.php';
<?
use
	Asenine\DB,
	Asenine\Element\Input,
	Asenine\User,
	Asenine\User\Manager,
	Asenine\User\Operation;

$username = null;

if( isset($_POST['register']) )
{
	try
	{
		DB::transactionStart();

		$username = $_POST['username'];
		$password = $_POST['password'];
		$passwordVerify = $_POST['passwordVerify'];

		Operation::verifyUsername($username);
		Operation::verifyPassword($password, $passwordVerify);


		$userID = Manager::addToDB();

		$query = DB::prepareQuery("UPDATE
				Asenine_Users
			SET
				isEnabled = 1,
				isAdministrator = 1,
				username = %s
			WHERE
				ID = %u",
			$username,
			$userID);

		DB::queryAndCountAffected($query);


		if( !Manager::setPassword($userID, $password) )
			throw New Exception(_('Lösenord kunde inte sättas'));


		if( !$User = User::login($username, $password) )
			throw New Exception(_('Användare kunde inte loggas in'));

		DB::transactionCommit();

		header('Location: /');

		exit();
	}
	catch(Exception $e)
	{
		DB::transactionRollback();

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
			<td><? echo _('Username'); ?></td>
			<td><? echo Input::text('username', $username); ?></td>
		</tr>
		<tr>
			<td><? echo _('Password'); ?></td>
			<td><? echo Input::password('password'); ?></td>
		</tr>
		<tr>
			<td><? echo _('Password Verify'); ?></td>
			<td><? echo Input::password('passwordVerify'); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="control">
				<input type="submit" name="register" value="<? echo _('Register'); ?>" />
			</td>
		</tr>
	</table>
</form>
<?
require DIR_ADMIN_ELEMENT . 'Footer.NotLoggedIn.inc.php';
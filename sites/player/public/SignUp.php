<?
namespace Cordless;

use
	\Asenine\DB,
	\Asenine\Element\Input,
	\Asenine\User\Operation;


define('NO_LOGIN', true);

require '../Init.Web.inc.php';

$usernameMinLen = User::USERNAME_MIN_LEN;
$usernameMaxLen = User::USERNAME_MAX_LEN;
$passwordMinLen = User::PASSWORD_MIN_LEN;

if( isset($_POST['signup']) )
{
	class SignUpException extends \Exception
	{}

	try
	{
		DB::transactionStart(false);

		$inviteCode = $_POST['inviteCode'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$passwordVerify = $_POST['passwordVerify'];


		$query = DB::prepareQuery("SELECT ID FROM Cordless_UserInvites WHERE userID IS NULL AND code = %s", $inviteCode);
		if( !$userInviteID = DB::queryAndFetchOne($query) )
			throw New SignUpException(_('Sorry, bad invite code :('));


		$usernameLen = mb_strlen($username);

		if( (isset($usernameMinLen) && $usernameLen < $usernameMinLen) || (isset($usernameMaxLen) && $usernameLen > $usernameMaxLen) )
			throw New SignUpException(sprintf(_('Username length illegal. Must be at least %d and at most %d characters'), $usernameMinLen, $usernameMaxLen));

		if( strlen($password) < $passwordMinLen)
			throw New SignUpException(sprintf(_('Password too short. Passwords must contain at least %d characters.'), User::PASSWORD_MIN_LEN));

		if( $password !== $passwordVerify )
			throw New SignUpException(_('Passwords does not match'));


		$query = DB::prepareQuery("SELECT COUNT(*) FROM Asenine_Users WHERE username = %s", $username);
		if( (bool)DB::queryAndFetchOne($query) )
			throw New SignUpException(sprintf(_('Username "%s" is already taken'), $username));


		$userID = \Asenine\User\Manager::addToDB();

		$query = DB::prepareQuery("UPDATE
				Asenine_Users
			SET
				isEnabled = 1,
				username = %s
			WHERE
				ID = %u",
			$username,
			$userID);
		DB::query($query);

		$query = DB::prepareQuery("INSERT INTO Asenine_UserGroupUsers (userGroupID, userID) SELECT ID, %d FROM Asenine_UserGroups WHERE name = 'Cordless'", $userID);
		DB::query($query);

		$query = DB::prepareQuery("UPDATE Cordless_UserInvites SET userID = %d WHERE ID = %d", $userID, $userInviteID);
		DB::query($query);


		if( !\Asenine\User\Manager::setPassword($userID, $password) )
			throw New Exception(_('Password set failed'));


		if( !$User = User::login($username, $password) )
			throw New Exception(_('User could not be logged in'));

		$_SESSION['User'] = $User;
		session_write_close();

		DB::transactionCommit();


		echo Element\Page\Message::notice(
			sprintf(_("Welcome %s!"), $User->username),
			_('You are now a Cordless user.') . ' ' . sprintf('<a href="./">%s &raquo;</a>', htmlspecialchars(_("Go listen!")))
		);

		exit();
	}
	catch(SignUpException $e)
	{
		DB::transactionRollback();

		echo Element\Page\Message::error(
			_("Sign Up Failed"),
			$e->getMessage()
		);

		exit();
	}
	catch(\Exception $e)
	{
		DB::transactionRollback();

		die(DEBUG ? $e->getMessage() : 'APPLICATION_ERROR');
	}
}

$css[] = '/css/Frontpage.css';

include DIR_ELEMENT . 'Header.Outside.inc.php';
?>
<h1><? echo _('Sign Up'); ?></h1>

<form action="<? echo getenv('REQUEST_URI'); ?>" method="post">

	<table class="signup">
		<tr>
			<td>
				<label><? echo _('Invite Code'); ?></label><br>
				<? echo Input::text('inviteCode', isset($_GET['invite']) ? $_GET['invite'] : null); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label><? echo _('Username'); ?></label><br>
				<? echo Input::text('username'); ?>
				<? printf(_("%d - %d characters"), $usernameMinLen, $usernameMaxLen); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label><? echo _('Password'); ?></label><br>
				<? echo Input::password('password')->addAttr('autocomplete', 'off'); ?> <? printf(_("At least %d characters"), $passwordMinLen); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label><? echo _('Repeat Password'); ?></label><br>
				<? echo Input::password('passwordVerify')->addAttr('autocomplete', 'off'); ?>
			</td>
		</tr>
		<tr>
			<td class="control">
				<button type="submit" name="signup" value="1"><? echo _('Sign Up'); ?></button>
			</td>
		</tr>
	</table>

</form>
<?
include DIR_ELEMENT . 'Footer.Outside.inc.php';
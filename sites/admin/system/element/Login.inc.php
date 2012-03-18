<?
use \Asenine\Element\Input;

if( isset($_POST['login']) ) ### If we end up here, user has tried to login, but $User->isLoggedIn() !== true
	$MB = Element\MessageBox::alert(_('Inloggning misslyckades'));

$pageTitle = _('Login');

require DIR_ADMIN_ELEMENT . 'Header.NotLoggedIn.inc.php';

if( isset($MB) ) echo $MB;
?>
<form action="<? echo getenv('REQUEST_URI'); ?>" method="post">
	<table>
		<tr>
			<td><? echo _('Username'); ?></td>
			<td><? echo Input::text('username', isset($_COOKIE['username']) ? $_COOKIE['username'] : null); ?></td>
		</tr>
		<tr>
			<td><? echo _('Password'); ?></td>
			<td><? echo Input::password('password')->addAttr('autocomplete', 'off'); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="control">
				<input type="submit" name="login" value="<? echo _('Log in'); ?>" />
			</td>
		</tr>
	</table>
</form>
<?
require DIR_ADMIN_ELEMENT . 'Footer.NotLoggedIn.inc.php';
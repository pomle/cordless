<?
if( isset($_POST['login']) ) ### If we end up here, user has tried to login, but $User->isLoggedIn() !== true
	$MB = \Element\MessageBox::alert(_('Inloggning misslyckades'));

$pageTitle = _('Login');

require DIR_ADMIN_ELEMENT . 'Header.NotLoggedIn.inc.php';

if( isset($MB) ) echo $MB;
?>
<form action="<? echo getenv('REQUEST_URI'); ?>" method="post">
	<table>
		<tr>
			<td><? echo _('Användarnamn'); ?></td>
			<td><? echo \Element\Input::text('username', $_COOKIE['username']); ?></td>
		</tr>
		<tr>
			<td><? echo _('Lösenord'); ?></td>
			<td><? echo \Element\Input::password('password')->addAttr('autocomplete', 'off'); ?></td>
		</tr>
		<tr>
			<td colspan="2" class="control">
				<input type="submit" name="login" value="<? echo _('Logga in'); ?>" />
			</td>
		</tr>
	</table>
</form>
<?
require DIR_ADMIN_ELEMENT . 'Footer.NotLoggedIn.inc.php';
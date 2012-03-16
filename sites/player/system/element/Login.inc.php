<?
namespace Cordless;

if( isset($_POST['login']) )
{
	echo Element\Page\Message::error(
		_("Login Failed"),
		_('And this extra page is to annoy you for being careless with your precious login details.')
			. ' ' . sprintf('<a href="/Login.php">%s</a>', htmlspecialchars(_("Try again?")))
	);

	die();
}

$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';

include DIR_ELEMENT . 'Header.Outside.inc.php';
?>
<h1><? echo _("Login"); ?></h1>

<a href="/SignUp.php"><? echo _("I have an invite"), ' &raquo;'; ?></a>

<form action="<? echo getenv('REQUEST_URI'); ?>" method="post">
	<table class="login">
		<tr>
			<td>
				<label><? echo _('Username'); ?></label><br>
				<? echo \Asenine\Element\Input::text('username', $username); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label><? echo _('Password'); ?></label><br>
				<? echo \Asenine\Element\Input::password('password')->addAttr('autocomplete', 'off'); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<button type="submit" name="login" value="1"><? echo _('Login'); ?></button>
			</td>
		</tr>
	</table>
</form>
<?
include DIR_ELEMENT . 'Footer.Outside.inc.php';

die();
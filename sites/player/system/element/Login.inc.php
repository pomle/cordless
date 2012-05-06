<?
namespace Cordless;

if( isset($_POST['login']) )
{
	echo Element\Page\Message::error(
		_("Login Failed"),
		_('And this extra page is to annoy you for being careless with your precious login details.')
			. '<br/>' . sprintf('<a href="%s">%s</a>', URL_PLAYER . 'Login.php', htmlspecialchars(_("Try again?")))
	);

	die();
}

$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';


include DIR_ELEMENT . 'Header.Outside.inc.php';

if( isset($_GET['userTrackID']) && $UserTrack = UserTrack::loadFromDB($_GET['userTrackID']) )
{
	if( isset($UserTrack->Image) )
		$trackImageURL = \Asenine\Media\Producer\Thumb::createFromHash($UserTrack->Image->mediaHash)->getCustom(200, 200, true);

	?>
	<section class="track">

		<?
		if( isset($trackImageURL) ) printf('<img src="%s">', $trackImageURL);
		?>

		<h1><? echo htmlspecialchars($UserTrack->title); ?></h1>
		<h2><? echo htmlspecialchars($UserTrack->artist); ?></h2>

	</section>
	<?
}
?>

<section class="login">

	<h1><? echo _("Login"); ?></h1>

	<a href="<? echo URL_PLAYER; ?>SignUp.php"><? echo _("I have an invite"), ' &raquo;'; ?></a>

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

</section>
<?
include DIR_ELEMENT . 'Footer.Outside.inc.php';

die();
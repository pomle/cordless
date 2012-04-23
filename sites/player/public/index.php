<?
namespace Cordless;

require '../Init.Web.inc.php';

$js[] = URL_PLAYER . 'js/jquery/jquery.dropUpload.js';

$js[] = URL_PLAYER . 'js/Functions.js';
$js[] = URL_PLAYER . 'js/APIController.js';
$js[] = URL_PLAYER . 'js/AudioController.js';
$js[] = URL_PLAYER . 'js/InterfaceController.js';
$js[] = URL_PLAYER . 'js/PlaylistController.js';
$js[] = URL_PLAYER . 'js/PanelController.js';
$js[] = URL_PLAYER . 'js/NowPlayingController.js';
$js[] = URL_PLAYER . 'js/Upload.js';

$js[] = URL_PLAYER . 'js/Main.js';
$js[] = URL_PLAYER . 'js/LastFM.js';


$goToURL = null;

if( isset($_GET['userTrackID']) )
	$goToURL = libraryURL('UserTrack-Control', array('userTrackID' => (int)$_GET['userTrackID']));


$style = $User->getSetting("WebUI_Theme_Style") ?: 'Charcoal';
$stylePath = 'theme/style/' . $style . '/';
define('URL_STYLE', URL_PLAYER . $stylePath);
define('DIR_STYLE', DIR_SITE . 'public/' . $stylePath);
require DIR_STYLE . 'Init.php';

$behavior = $User->getSetting("WebUI_Theme_Behavior") ?: 'Default';
$behaviorPath = 'theme/behavior/' . $behavior . '/';
define('URL_BEHAVIOR', URL_PLAYER . $behaviorPath);
define('DIR_BEHAVIOR', DIR_SITE . 'public/' . $behaviorPath);
require DIR_BEHAVIOR . 'Init.php';


$bodyClass = array();
$bodyClass[] = $User->getSetting("WebUI_PlayQueue_Locked");
$bodyClass[] = $User->getSetting("WebUI_Upload_Locked");
$bodyClass[] = $User->getSetting("WebUI_Tracklist_View_Mode") ?: 'tracklistTiles';
$bodyClass[] = $User->getSetting("WebUI_Global_Background_isLocked");


?><!DOCTYPE html>
<html lang="en">
<head>
	<?
	if( isset($pageTitle) ) printf('<title>%s</title>', htmlspecialchars($pageTitle));

	foreach($css as $path)
		printf('<link rel="stylesheet" type="text/css" href="%s">', $path);

	?>
	<style type="text/css">
		body
		{
			<? if( $bg = $User->getSetting('WebUI_Global_Background_URL') ) printf("background-image: url('%s');", $bg); ?>
		}
	</style>

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<meta name="viewport" content="width=800">
</head>

<body
	id="cordless"
	class="<? echo trim(join(' ', $bodyClass)); ?>"
	data-cordless-api-url="<? echo htmlspecialchars(URL_API); ?>"
	data-last-fm-api-key="<? echo htmlspecialchars(LAST_FM_API_KEY); ?>
	">
	<header id="control">

		<div class="inner">

			<form action="./ajax/Panel.php?type=Library&amp;name=Tracks-Search" method="get" id="search">
				<div class="search">
					<input type="text" name="q" value="<? if( isset($_COOKIE['searchQuery']) ) echo htmlspecialchars($_GET['searchQuery']); ?>">
				</div>
			</form>

			<div class="torus">
				<a href="#Home" class="home panelLibrary"></a>

				<nav class="library">
					<div class="goTo">
						<a href="#Home" class="panelLibrary home" title="<? echo htmlspecialchars(_("Go to Home")); ?>"><? echo htmlspecialchars(_("Home")); ?></a>
						<a href="#Back" class="historySkip prev" rel="-1" title="<? echo htmlspecialchars(_("Go Backwards in History (Q)")); ?>"><? echo htmlspecialchars(_("Backward")); ?></a>
						<a href="#Forward" class="historySkip next" rel="1" title="<? echo htmlspecialchars(_("Go Forwards in History (W)")); ?>"><? echo htmlspecialchars(_("Forward")); ?></a>
					</div>
				</nav>
			</div>

			<?
			$Player = new Element\Player();
			echo $Player;
			?>

		</div>

	</header>

	<section id="upload" class="sidebar">
		<div class="control">
			<div class="icon logo"></div>

			<a href="#" class="icon lock" title="<? echo htmlspecialchars(_('Lock panel in extended state (U)')); ?>"><? echo _('Lock'); ?></a>
			<a href="#" class="icon close" title="<? echo htmlspecialchars(_('Hide panel before upload has finished')); ?>"><? echo _('Hide'); ?></a>
		</div>

		<div class="content">
			<?
			$UploadForm = new Element\Upload( apiLink('Import.ReceiveFile') );
			echo $UploadForm;
			?>
			<a href="#Tracks-AddTime" class="panelLibrary"><? echo _("Go to Recent Tracks"), ' &raquo;'; ?></a>
		</div>
	</section>

	<section id="playqueue" class="sidebar">
		<div class="control">
			<div class="icon logo"></div>

			<a href="#" class="icon lock" title="<? echo htmlspecialchars(_('Lock panel in extended state (P)')); ?>"><? echo _('Lock'); ?></a>
		</div>

		<div class="content">
			<?
			#$Fetch = new \Fetch\UserTrack($User);

			#$userTracks = $Fetch->getLastPlaylist();

			$Playlist = new Element\Playlist(); #\Element\Playlist::createFromUserTracks($userTracks);

			echo $Playlist;
			?>
		</div>
	</section>

	<section
		id="library"
		data-goToURL="<? echo htmlspecialchars($goToURL); ?>"
		>

		<nav class="history">
			<div class="trail">
			</div>
		</nav>

		<div class="content">
			<?
			loadPanel('Library', 'Home');
			?>
		</div>

	</section>

	<footer class="footer">

	</footer>
	<?
	foreach($js as $path)
		printf('<script type="text/javascript" src="%s"></script>', $path);
	?>
</body>
</html>

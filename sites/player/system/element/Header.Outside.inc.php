<?
namespace Cordless;

$css[] = URL_PLAYER . 'css/Outside.css';

if(
	( isset($UserTrack) && $UserTrack instanceof UserTrack ) ||
	( isset($_GET['userTrackID']) && $UserTrack = UserTrack::loadFromDB($_GET['userTrackID']) )
)
{
	if( isset($UserTrack->Image) )
		$imageURL = getUserTrackImageURL($UserTrack->Image->mediaHash);

	$pageTitle = str_replace('%TRACK_TITLE%', $UserTrack->getCaption(), _('%TRACK_TITLE% - Listen @ Cordless'));
}

include DIR_ELEMENT . 'Header.Minimal.inc.php';
?>
<div class="content">
	<div class="logo">
		<a href="./"><img src="<? echo URL_PLAYER; ?>img/Cordless-Logo-Frontpage.png"></a>
	</div>

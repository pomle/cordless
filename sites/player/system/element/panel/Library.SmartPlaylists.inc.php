<?
namespace Cordless;

echo Element\Library::head(_('Smart Playlists'));

$qs = sprintf('userID=%d', isset($params->userID) ? $params->userID : $User->userID);
?>

<ul>
	<li><? echo libraryLink(_('Added During'), 'SmartPlaylists-AddedDuring', $qs); ?></li>
	<li><? echo libraryLink(_('Most Played'), 'SmartPlaylists-MostPlayed', $qs); ?></li>
	<li><? echo libraryLink(_('Random'), 'Tracks-Random', $qs); ?></li>
	<li><? echo libraryLink(_('Upload Events'), 'SmartPlaylists-UploadEvents', $qs); ?></li>
</ul>
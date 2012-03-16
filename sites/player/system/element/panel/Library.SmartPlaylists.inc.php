<?
namespace Cordless;

echo Element\Library::head(_('Smart Playlists'));
?>

<ul>
	<li><? echo libraryLink(_('Added During'), 'SmartPlaylists-AddedDuring'); ?></li>
	<li><? echo libraryLink(_('Most Played'), 'SmartPlaylists-MostPlayed'); ?></li>
	<li><? echo libraryLink(_('Random'), 'Tracks-Random'); ?></li>
	<li><? echo libraryLink(_('Upload Events'), 'SmartPlaylists-UploadEvents'); ?></li>
</ul>
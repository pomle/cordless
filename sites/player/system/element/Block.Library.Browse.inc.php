<?
namespace Cordless;

$params = isset($userID) ? array('userID' => $userID) : null;
?>
<section class="browse">

	<h3><? echo htmlspecialchars(_("Browse")); ?></h3>

	<ul>
		<li><? echo libraryLink(_("Albums"), 'Index-Albums', $params); ?></li>
		<li><? echo libraryLink(_("Artists"), 'Index-Artists', $params); ?></li>
		<li><? echo libraryLink(_("Playlists"), 'Index-Playlists', $params); ?></li>
		<li><? echo libraryLink(_("SmartPlaylists"), 'SmartPlaylists', $params); ?></li>
	</ul>

	<ul>
		<li><? echo libraryLink(_("Recently Added"), 'Tracks-AddTime', $params); ?></li>
		<li><? echo libraryLink(_("Recently Played"), 'Tracks-PlayTime', $params); ?></li>
		<li><? echo libraryLink(_("Recently Starred"), 'Tracks-StarTime', $params); ?></li>
	</ul>

	<ul>
		<li><? echo libraryLink(_("Friends"), 'Index-Friends', $params); ?></li>
	</ul>

</section>
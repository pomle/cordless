<?
namespace Cordless;

use \Asenine\DB;

$keepSession = true;

function APIMethod($User, $params)
{
	foreach(array('last_fm_scrobble', 'last_fm_love_starred_tracks', 'last_fm_unlove_unstarred_tracks') as $key)
		$$key = (bool)(isset($params->$key) ? $params->$key : false);

	$query = DB::prepareQuery("INSERT INTO
		Cordless_Users (
			userID,
			last_fm_scrobble,
			last_fm_love_starred_tracks,
			last_fm_unlove_unstarred_tracks
		) VALUES(
			%u,
			%d,
			%d,
			%d
		) ON DUPLICATE KEY UPDATE
			last_fm_scrobble = VALUES(last_fm_scrobble),
			last_fm_love_starred_tracks = VALUES(last_fm_love_starred_tracks),
			last_fm_unlove_unstarred_tracks = VALUES(last_fm_unlove_unstarred_tracks)",
		$User->userID,
		$last_fm_scrobble,
		$last_fm_love_starred_tracks,
		$last_fm_unlove_unstarred_tracks);

	DB::query($query);

	$User->last_fm_scrobble					= (bool)$last_fm_scrobble;
	$User->last_fm_love_starred_tracks		= (bool)$last_fm_love_starred_tracks;
	$User->last_fm_unlove_unstarred_tracks	= (bool)$last_fm_unlove_unstarred_tracks;

	return _("User Settings Saved");
}
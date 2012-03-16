<?
namespace Cordless;

require '../../Init.Web.inc.php';

$last_fm_string_html = '<span style="color: #e60000;">Last.fm</span>';

try
{
	if( !isset($_GET['token']) ) throw New \Exception('Token missing');

	$LastFM = getLastFM();

	if( !$Session = $LastFM->getSession($_GET['token']) )
		throw New \Exception("Could not obtain Last fm Session");


	$User->last_fm_username = $Session->name;
	$User->last_fm_key = $Session->key;
	$User->last_fm_scrobble = true;
	$User->last_fm_love_starred_tracks = false;
	$User->last_fm_unlove_unstarred_tracks = false;

	User::saveToDB($User);

	echo Element\Page\Message::notice(
		sprintf(_("%s Connect Successful"), $last_fm_string_html),
		sprintf(_("You have successfully connected your Last.fm account to Cordless with username %s!"), '<em>' . htmlspecialchars($User->last_fm_username) . '</em>') .
		sprintf("<br><small>%s</small>", _("Psst... you can close this tab/window. We kept this action in a new one."))
	);
}
catch(\Exception $e)
{
	echo Element\Page\Message::error(
		sprintf(_("%s Connect Failed"), $last_fm_string_html),
		DEBUG ? $e->getMessage() : _("Something went wrong. Sorry")
	);
}
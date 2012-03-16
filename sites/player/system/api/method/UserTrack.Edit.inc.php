<?
namespace Cordless;

function APIMethod($User, $params)
{
	ensureParams($params, 'action');

	$action = $params['action'];

	switch($action)
	{
		case 'delete':
			$UserTrack = getUserTrack($params, $User);
			UserTrack::removeFromDB($UserTrack);

			return _("Track deleted");
		break;

		case 'grab':
			$UserTrack = getUserTrack($params);

			if( $UserTrack_Existing = UserTrack::loadByTrack($User, $UserTrack->Track->trackID) )
				return array('userTrackID' => $UserTrack_Existing->userTrackID);

			$UserTrack->takeOwnership($User->userID);

			UserTrack::saveToDB($UserTrack);

			return array('userTrackID' => $UserTrack->userTrackID);
		break;

		case 'update':
			$UserTrack = getUserTrack($params, $User);

			if( isset($params['artist']) )
				$UserTrack->artist = (string)$params['artist'];

			if( isset($params['title']) )
				$UserTrack->title = (string)$params['title'];

			/*if( isset($params['album']) )
				$UserTrack->album = (string)$params['album'];

			if( isset($params['year']) )
				$UserTrack->year = (string)$params['year'];*/

			#$UserTrack->isStarred = ( isset($params['isStarred']) && (bool)$params['isStarred'] );

			if( isset($params['filename']) )
				$UserTrack->filename = (string)$params['filename'];

			UserTrack::saveToDB($UserTrack);

			return _("Track successfully saved");
		break;



		default:
			throw new APIException("Invalid action: $action");
	}
}
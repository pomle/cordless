<?
namespace Cordless;

function APIMethod(User $User, $params)
{
	if( !isset($params->action) )
		throw new ParamException('action');

	if( !isset($params->playlistID) )
		throw new ParamException('playlistID');

	$playlistID = (int)$params->playlistID ?: null;

	switch($params->action)
	{
		case 'delete':
			if( !$playlistID || !$Playlist = Playlist::loadFromDB($playlistID) )
				throw New APIException("Invalid playlistID");

			Playlist::removeFromDB($Playlist);

			return;
		break;

		case 'save':
			if( !$playlistID || !$Playlist = Playlist::loadFromDB($playlistID) )
			{
				if( !isset($params->title) )
					throw new ParamException('title');

				$Playlist = new Playlist();
			}

			if( isset($params->title) )
				$Playlist->title = $params->title;

			if( isset($params->userTrackIDs) && is_array($params->userTrackIDs) )
			{
				$Playlist->clearTracks();

				$userTracks = UserTrack::loadFromDB($params->userTrackIDs);

				foreach($userTracks as $UserTrack)
					$Playlist->addUserTrack($UserTrack);
			}

			Playlist::saveToDB($Playlist);

			$Playlist->assignCreator($User);

			return array('playlistID' => $Playlist->playlistID);
		break;
	}
}
<?
namespace Cordless\Fetch;

use \Asenine\DB;

class UserTrack
{
	private
		$lastQuery;

	protected
		$userID;

	public
		$method,
		$args,
		$offset,
		$limit;


	public function __construct(\Cordless\User $User, $method = null)
	{
		$this->userID = $User->userID;

		$this->method = $method;
		$this->args = array_slice(func_get_args(), 2);
		$this->offset = $this->limit = 0;
	}

	public function __invoke()
	{
		if( !method_exists($this, $this->method) )
			throw New \Exception(sprintf("Method %s::%s does not exist", __CLASS__, $this->method));

		return call_user_func_array(array($this, $this->method), $this->args);
	}


	protected function byAddTime($timeStart = null, $timeEnd = null, Array $userIDs = null)
	{
		$this->method = __FUNCTION__;
		$this->args = func_get_args();

		$query = DB::prepareQuery("SELECT
				ut.ID
			FROM
				Cordless_UserTracks ut
			WHERE
				ut.userID IN %a
				AND (0 = %d OR ut.timeCreated BETWEEN %d AND %d)
			ORDER BY
				ut.timeCreated DESC",
			$userIDs ?: $this->userID,
			(bool)$timeStart,
			$timeStart,
			$timeEnd ?: time());

		return $this->queryToUserTracks($query);
	}

	protected function byAlbum(Array $albumIDs, Array $userIDs = null)
	{
		$this->method = __FUNCTION__;
		$this->args = func_get_args();

		$query = DB::prepareQuery("SELECT
				ut.ID
			FROM
				Cordless_Albums a
				JOIN Cordless_AlbumTracks at ON at.albumID = a.ID
				JOIN Cordless_UserTracks ut ON ut.trackID = at.trackID
			WHERE
				ut.userID IN %a
				AND
				(
					a.ID IN %a
				)
			ORDER BY
				at.trackNO ASC",
			$userIDs ?: $this->userID,
			$albumIDs);

		return $this->queryToUserTracks($query);
	}

	protected function byArtist($name, Array $userIDs = null)
	{
		$this->method = __FUNCTION__;
		$this->args = func_get_args();

		$query = DB::prepareQuery("SELECT
				ut.ID
			FROM
				Cordless_UserTracks ut
			WHERE
				ut.userID IN %a
				AND
				(
					ut.artist = %s
				)
			ORDER BY
				ut.title ASC",
			$userIDs ?: $this->userID,
			$name);

		return $this->queryToUserTracks($query);
	}

	protected function byPlaylist($playlistID)
	{
		$this->method = __FUNCTION__;
		$this->args = func_get_args();

		$query = DB::prepareQuery("SELECT
				ut.ID
			FROM
				Cordless_Playlists p
				JOIN Cordless_PlaylistTracks pt ON pt.playlistID = p.ID
				JOIN Cordless_UserTracks ut ON ut.trackID = pt.trackID AND ut.userID = %d
			WHERE
				p.ID = %d
			ORDER BY
				pt.sortOrder ASC",
			$this->userID,
			$playlistID);

		return $this->queryToUserTracks($query);
	}

	protected function byPlayRank($uts_f = null, $uts_t = null, Array $userIDs = null)
	{
		$this->method = __FUNCTION__;
		$this->args = func_get_args();

		$query = DB::prepareQuery("SELECT
				ut.ID
			FROM
				Cordless_UserTracks ut
				LEFT JOIN Cordless_UserTrackPlays utp ON utp.userTrackID = ut.ID
			WHERE
				ut.userID IN %a
				AND (0 = %d OR utp.timeCreated BETWEEN %d AND %d)
			GROUP BY
				ut.ID
			ORDER BY
				COUNT(*) DESC,
				ut.timeLastPlayed DESC",
			$userIDs ?: $this->userID,
			(bool)$uts_f,
			$uts_f,
			$uts_t ?: time());

		return $this->queryToUserTracks($query);
	}

	protected function byPlayTime(Array $userIDs = null)
	{
		$this->method = __FUNCTION__;
		$this->args = func_get_args();

		$query = DB::prepareQuery("SELECT
				ID
			FROM
				Cordless_UserTracks
			WHERE
				userID IN %a
			ORDER BY
				timeLastPlayed DESC",
			$userIDs ?: $this->userID);

		return $this->queryToUserTracks($query);
	}

	protected function bySearch($string, Array $userIDs = null)
	{
		$this->method = __FUNCTION__;
		$this->args = func_get_args();

		$userIDs = $userIDs ?: $this->userID;

		### Search own and friends libraries and always prefers owned tracks

		$query = DB::prepareQuery("SELECT
				IFNULL(ut_owner.ID, ut.ID) AS userTrackID
			FROM
				Cordless_UserTracks ut
				LEFT JOIN Cordless_UserTracks ut_owner ON ut_owner.trackID = ut.trackID AND ut_owner.userID = %d
				JOIN Cordless_Tracks t ON t.ID = ut.trackID
				JOIN Cordless_TrackArtists ta ON ta.trackID = t.ID
				JOIN Cordless_Artists a ON a.ID = ta.artistID
			WHERE
				ut.userID IN %a
				AND
				(
					CONCAT_WS(' ', IFNULL(ut_owner.artist, a.name), IFNULL(ut_owner.title, t.title)) LIKE %S
				)
			GROUP BY
				t.ID
			ORDER BY
				IFNULL(ut_owner.playcount, ut.playcount) DESC",
			$this->userID,
			$userIDs,
			$string);

		return $this->queryToUserTracks($query);
	}

	protected function byStarTime($uts_f = null, $uts_t = null, Array $userIDs = null)
	{
		$this->method = __FUNCTION__;
		$this->args = func_get_args();

		$query = DB::prepareQuery("SELECT
				ut.ID
			FROM
				Cordless_UserTracks ut
				JOIN Cordless_UserTracksStarred uts ON uts.userTrackID = ut.ID
			WHERE
				ut.userID IN %a
				AND (0 = %d OR uts.timeCreated BETWEEN %d AND %d)
			GROUP BY
				ut.ID
			ORDER BY
				COUNT(*) DESC,
				uts.timeCreated DESC",
			$userIDs ?: $this->userID,
			(bool)$uts_f,
			$uts_f,
			$uts_t ?: time());

		return $this->queryToUserTracks($query);
	}

	public function getUserTracks($userTrackIDs)
	{
		$userTracks = \Cordless\UserTrack::loadFromDB($userTrackIDs, $this->userID);

		return $userTracks;
	}

	public function queryToUserTracks($query) ### Helper function that appends LIMIT clause if $this->limit is set and avoids extra query if ID count is zero
	{
		if( $this->limit )
		{
			$this->offset = (int)$this->offset;
			$this->limit = (int)$this->limit;
			$query .= sprintf(" LIMIT %d, %d", $this->offset, $this->limit+1);
		}

		#$this->lastQuery = $query;

		$userTrackIDs = DB::queryAndFetchArray($query);

		$resultLen = count($userTrackIDs);

		$this->hasMore = $this->limit && ($resultLen > $this->limit);

		if( $resultLen == 0 )
			return array();

		return self::getUserTracks(array_slice($userTrackIDs, 0, $this->limit ?: null));
	}

	public function pageSkip($diff)
	{
		if( $this->limit )
			$this->recordSkip($this->limit * $diff);
	}

	public function recordSkip($diff)
	{
		$this->offset += $diff;
	}
}
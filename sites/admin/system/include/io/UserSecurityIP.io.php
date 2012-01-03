<?
class UserSecurityIPIO extends AjaxIO
{
	public function save()
	{
		ensurePolicies('AllowEditUser');

		$this->importArgs('policy', 'spanStart', 'spanEnd');

		#var_dump($this->spanStart, $this->spanEnd);

		if( !$this->spanStart = ip2long($this->spanStart) )
			throw New Exception(_('IP-adress i från-fältet är felinmatad'));

		if( !$this->spanEnd = ip2long($this->spanEnd) )
			$this->spanEnd = $this->spanStart;

		$this->spanAppend = $this->spanEnd - $this->spanStart;

		$query = DB::prepareQuery("INSERT INTO
			UserSecurityIPs (
				ID,
				userID,
				policy,
				spanStart,
				spanAppend
			) VALUES(
				NULLIF(%u, 0),
				%u,
				NULLIF(%s, ''),
				%u,
				%u
			) ON DUPLICATE KEY UPDATE
				policy = VALUES(policy),
				spanStart = VALUES(spanStart),
				spanAppend = VALUES(spanAppend)",
			$this->userSecurityIPID,
			$this->userID,
			$this->policy,
			$this->spanStart,
			$this->spanAppend);

		#throw New Exception($query);

		$this->userSecurityIPID = DB::queryAndGetID($query);

		$this->load();

		Message::addNotice(MESSAGE_ROW_UPDATED);
	}

	public function load()
	{
		ensurePolicies('AllowViewUser');

		global $result;

		$query = DB::prepareQuery("SELECT
				ID AS userSecurityIPID,
				userID,
				policy,
				spanStart,
				spanStart + spanAppend AS spanEnd
			FROM
				UserSecurityIPs
			WHERE ID = %u",
			$this->userSecurityIPID);

		$result = DB::queryAndFetchOne($query);

		### If any formatting needs to be done
		$result['spanStart'] = long2ip($result['spanStart']);
		$result['spanEnd'] = long2ip($result['spanEnd']);
	}

	public function delete()
	{
		ensurePolicies('AllowEditUser');

		$query = \DB::prepareQuery("DELETE FROM UserSecurityIPs WHERE ID = %u", $this->userSecurityIPID);
		\DB::queryAndCountAffected($query);

		Message::addNotice(MESSAGE_ROW_DELETED);
	}
}

### $action contains the initial function that is called, for example 'save'
$AjaxIO = new UserSecurityIPIO($action, array('userSecurityIPID', 'userID'));
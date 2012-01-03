<?
class SkeletonIO extends AjaxIO
{
	public function save()
	{
		ensurePolicies('AllowEditSkeleton');

		$this->importArgs('param3', 'param4');

		$query = DB::prepareQuery("INSERT INTO
			SkeletonTable (
				a,
				b,
				c,
				d)
			VALUES(
				%u,
				%u,
				%s,
				%s)",
			$this->param1,
			$this->param2,
			$this->param3,
			$this->param4);

		DB::queryAndGetID($query);

		Message::addNotice(_('Klar'));
	}

	public function load()
	{
		ensurePolicies('AllowViewSkeleton');

		global $result; // Holds what is returned to browser

		$query = DB::prepareQuery("SELECT
				ID AS skeletonID,
				value1,
				value2
			FROM
				SkeletonTable
			WHERE ID = %u",
			$this->param1);

		$result = DB::queryAndFetchOne($query);

		### If any formatting needs to be done
		$result['value1'] = $this->Format::timestamp($result['value1']);
	}
}

### $action contains the initial function that is called, for example 'save'
$AjaxIO = new SkeletonIO($action, array('param1', 'param2'));
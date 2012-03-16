<?
namespace Asenine\Manager;

class Policy extends Common\DB
{
	public static function loadFromDB($policyIDs)
	{
		$policies = array();

		$query = \DB::prepareQuery("SELECT
				p.ID AS policyID,
				p.policy,
				p.description
			FROM
				Policies p
			WHERE
				p.ID IN %a", $policyIDs);

		$result = \DB::queryAndFetchResult($query);

		while($policy = \DB::assoc($result))
		{
			$Policy = new \stdClass();

			$Policy->policyID = (int)$policy['policyID'];
			$Policy->policy = $policy['policy'];
			$Policy->name = $Policy->policy;
			$Policy->description = $policy['description'];

			$policies[$Policy->policyID] = $Policy;
		}

		return $policies;
	}
}
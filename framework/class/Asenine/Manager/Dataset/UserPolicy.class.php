<?
namespace Asenine\Manager\Dataset;

class UserPolicy
{
	public static function getAvailable()
	{
		$query = "SELECT ID, policy, description FROM Policies";
		return \DB::queryAndFetchArray($query);
	}

	public static function getDescription($policyIDs)
	{
		$query = \DB::prepareQuery("SELECT ID, description FROM Asenine_Policies WHERE ID IN %a", $policyIDs);
		return \DB::queryAndFetchArray($query);
	}
}
<?
namespace Asenine;

class IPPool
{
	public
		$ranges;

	public function __construct()
	{
		$this->ranges = array();
	}


	public function addRange($startIP, $endIP = null)
	{
		if( is_null($endIP) ) $endIP = $startIP;

		$startIP = is_int($startIP) ? $startIP : ip2long($startIP);
		$endIP = is_int($endIP) ? $endIP : ip2long($endIP);

		$this->ranges[] = array(min($startIP, $endIP), max($startIP, $endIP));
	}

	public function getRangeAsIPs()
	{
		$rangeASIPs = $this->ranges;
		foreach($rangeASIPs as &$range)
			$range = array_map('long2ip', $range);

		return $range;
	}

	public function hasIP($ip)
	{
		if( !is_int($ip) )
			$ip = ip2long($ip);

		foreach($this->ranges as $range)
			if( $ip >= $range[0] && $ip <= $range[1] ) return true;

		return false;
	}
}
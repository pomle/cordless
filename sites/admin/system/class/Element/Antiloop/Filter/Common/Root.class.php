<?
namespace Element\Antiloop\Filter\Common;

interface _Root
{
	public function __toString();
	public function importParams(array $params);
}

abstract class Root implements _Root
{
}
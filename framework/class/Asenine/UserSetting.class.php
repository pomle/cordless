<?
namespace Asenine;

class UserSetting
{
	protected
		$settings;

	public function __construct()
	{
		$this->settings = array();
	}

	public function __get($key)
	{
		return isset($this->settings[$key]) ? $this->settings[$key] : null;
	}

	public function __isset($key)
	{
		return isset($this->settings[$key]);
	}

	public function __set($key, $value) ### Notice value is only set if not null
	{
		if( !is_null($value) )
			$this->settings[$key] = $value;
		else
			unset($this->settings[$key]);
	}
}
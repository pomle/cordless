<?
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

	### Notice that value is only set if not null
	public function __set($key, $value)
	{
		if( !is_null($value) )
			$this->settings[$key] = $value;

		else
			unset($this->settings[$key]);
	}
}
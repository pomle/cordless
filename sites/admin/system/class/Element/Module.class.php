<?
namespace Element;

class Module
{
	public function __construct()
	{
		$args = func_get_args();
		$elementModule = array_shift($args);

		ob_start();
		require DIR_ADMIN_SYSTEM . 'include/elementModules/' . $elementModule . '.inc.php';
		$this->html = ob_get_clean();
	}

	public function __toString()
	{
		return $this->html;
	}
}
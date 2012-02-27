<?
class AjaxIO
{
	final public function __construct($action, $importArgs = array())
	{
		if( $action === 'new' ) $action = 'create'; //Workaround for the reserved word "new"

		foreach($importArgs as $arg)
			$this->importArgs($arg);

		if( method_exists($this, $action) )
			$this->$action();
		#else
			#throw New Exception(get_class($this) . '::' . $action . ' does not exist'); // JS Relies on this being an OK scenario
	}


	public function importArgs()
	{
		$varnames = func_get_args();
		foreach($varnames as $varname)
		{
			if( !isset($this->$varname) ) ### Don't overwrite
			{
				if( isset($_GET[$varname]) )
					$this->$varname = $_GET[$varname];
				elseif( isset($_POST[$varname]) )
					$this->$varname = $_POST[$varname];
				else
					$this->$varname = null;
			}
		}
	}

	public function parseDateTime($timeString)
	{
		return strtotime($timeString);
	}

	public function parseFloat($floatString)
	{
		return (float)str_replace(',', '.', $floatString);
	}

	public function setResult($newResult)
	{
		global $result;
		$result = $newResult;
	}
}
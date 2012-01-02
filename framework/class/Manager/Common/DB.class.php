<?
namespace Manager\Common;

abstract class DB extends Root
{
	final public static function loadOneFromDB()
	{
		$args = func_get_args();
		$args[0] = array($args[0]);
		return reset(call_user_func_array(array('static', 'loadFromDB'), $args));
	}

	final public static function saveOneToDB()
	{
		$args = func_get_args();
		$args[0] = array($args[0]);
		return call_user_func_array(array('static', 'saveToDB'), $args);
	}
}
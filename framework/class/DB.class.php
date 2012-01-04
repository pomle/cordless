<?
class DBException extends \Exception
{}

class DB
{
	const PROVIDER = 'MySQL';

	private static $MySQLi;

	public static
		$queryCount,
		$lastQuery,
		$allQueries;

	private static
		$vars,
		$varIterator;


	public static function assoc($Result)
	{
		return $Result->fetch_assoc();
	}

	public static function row($Result)
	{
		return $Result->fetch_row();
	}

	public static function countRows($Result)
	{
		return $Result->num_rows;
	}

	public static function fetch($query)
	{
		return self::queryAndFetchResult($query);
	}

	public static function init()
	{
		self::$queryCount = 0;
		if( is_null(self::$MySQLi) )
		{
			self::$MySQLi = mysqli_init();

			if( mysqli_real_connect(self::$MySQLi, DB_HOST, DB_USER, DB_PASS, DB_NAME) )
				mysqli_set_charset(self::$MySQLi, DB_CHARSET);
		}
		return true;
	}

	public static function escapeString($value)
	{
		if( !is_string($value) ) throw New DBException(sprintf('%s requires arg #1 to be string, %s given', __METHOD__, gettype($value)));
		return mysqli_real_escape_string(self::$MySQLi, $value);
	}

	public static function prepareQuery()
	{
		$vars = func_get_args();
		$query = array_shift($vars);

		$query = str_replace('||', defined('DEFAULT_COLLATION') ? constant('DEFAULT_COLLATION') : 'utf8_general_ci', $query);

		self::$vars = $vars;
		self::$varIterator = 0;

		$query = preg_replace_callback('/%([AaduFfSs])/', array('self', 'prepareVariable'), $query);

		self::$vars = null;

		return $query;
	}

	protected static function prepareVariable($matches)
	{
		$placeholder = $matches[1];
		$var = self::$vars[self::$varIterator++];

		#var_dump($placeholder);

		switch($placeholder)
		{
			### Array of integers
			case 'a':
				$var = array_map('intval', (array)$var + array(0));
				return '(' . join(',', $var) . ')';

			### Array of strings
			case 'A':
				$var = array_map(array('self', 'escapeString'), $var);
				return "('" . join("','", $var) . "')";

			### Signed integer
			case 'd':
				return sprintf('%d', $var);

			case 'u':
				return sprintf('%u', $var);

			### Float
			case 'F':
			case 'f':
				return sprintf('%F', (float)$var);

			### LIKE match string
			case 'S': ### Notice that this case continues to next on purpose
				$var = '%' . str_replace('*', '%', $var) . '%';

			### String
			case 's':
				return "'" . self::escapeString((string)$var) . "'";
		}

		return '0';
	}

	public static function query($query)
	{
		return self::queryAndFetchResult($query);
	}

	public static function queryAndCountAffected($query)
	{
		if( self::queryAndFetchResult($query) )
			return self::$MySQLi->affected_rows;
		return false;
	}

	public static function queryAndFetchArray($query)
	{
		$Result = self::queryAndFetchResult($query);

		$array = array();

		while($row = $Result->fetch_assoc())
		{
			switch($Result->field_count)
			{
				case 1:
					$array[] = current($row);
				break;

				case 2:
					list($id, $value) = array_values($row);
					$array[$id] = $value;
				break;

				default:
					list($id) = array_values($row);
					$array[(int)$id] = array_slice($row, 1);
			}
		}

		return $array;
	}

	public static function queryAndFetchOne($query)
	{
		$Result = self::queryAndFetchResult($query);

		if( $Result->num_rows > 0 ) {

			$values = $Result->fetch_assoc(); ### Adds keyword-based values

			if( count($values) == 1 ) return reset($values); ### Return first value if only one

			return $values;
		}

		return false;
	}

	public static function queryAndFetchResult($query)
	{
		self::$queryCount++;
		#self::$lastQuery = $query;
		#self::$allQueries[] = $query;

		if( !$Result = mysqli_query(self::$MySQLi, $query) )
		{
			if( DEBUG )
				throw new Exception(mysqli_error(self::$MySQLi)."\n".$query);
			else
			{
				trigger_error("Query Failed: ".$query."\nMessage: ".mysqli_error(self::$MySQLi), E_USER_WARNING);
				throw new Exception('Database Error');
			}
		}

		return $Result;
	}

	public static function queryAndGetID($query)
	{
		if( self::queryAndFetchResult($query) )
			return self::$MySQLi->insert_id;

		return false;
	}

	public static function transactionCommit()
	{
		return self::$MySQLi->commit();
	}

	public static function transacationRollback()
	{
		return self::$MySQLi->rollback();
	}

	public static function transactionEnd()
	{
		return self::$MySQLi->autocommit(true);
	}

	public static function transactionStart()
	{
		return self::$MySQLi->autocommit(false);
	}
}

DB::init();
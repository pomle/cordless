<?
namespace Asenine;

class DBException extends \Exception
{}

class DB
{
	private static
		$connections = array(),
		$PDO = null;

	private static
		$vars,
		$varIterator;

	public static
		$queryCount = 0,
		$lastQuery = null,
		$allQueries = array();


	public static function addPDO(\PDO $PDO, $name = null)
	{
		if( $name )
			self::$connections[$name] = $PDO;
		else
			self::$connections[] = $PDO;

		if( !self::$PDO )
			self::$PDO = $PDO;
	}


	public static function assoc($Result)
	{
		return $Result->fetch(\PDO::FETCH_ASSOC);
	}

	public static function row($Result)
	{
		return $Result->fetch(\PDO::FETCH_NUM);
	}

	public static function countRows($Result)
	{
		return $Result->countRows();
	}

	public static function fetch($query)
	{
		return self::queryAndFetchResult($query);
	}


	public static function escapeString($value)
	{
		if( !is_string($value) ) throw New DBException(sprintf('%s requires arg #1 to be string, %s given', __METHOD__, gettype($value)));
		return self::$PDO->quote($value);
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
				return self::escapeString((string)$var);
		}

		return '0';
	}

	public static function queryAndCountAffected($query)
	{
		return self::query($query, true);
	}

	public static function queryAndFetchArray($query)
	{
		$Stmt = self::queryAndFetchResult($query);

		$array = array();

		$c = $Stmt->columnCount();

		while($row = $Stmt->fetch(\PDO::FETCH_ASSOC))
		{
			switch($c)
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
				break;
			}
		}

		return $array;
	}

	public static function queryAndFetchOne($query)
	{
		if( !$Stmt = self::queryAndFetchResult($query) )
			return false;

		if( $Stmt->rowCount() == 0 )
			return false;

		$values = $Stmt->fetch(\PDO::FETCH_ASSOC);

		if( count($values) == 1 )
			return reset($values); ### Return first value if only one

		return $values;
	}

	public static function queryAndFetchResult($query)
	{
		return self::query($query, false);
	}

	public static function queryAndGetID($query)
	{
		if( $Stmt = self::queryAndFetchResult($query) )
			return self::$PDO->lastInsertId();

		return false;
	}

	public static function query($query, $returnAffected = false)
	{
		self::$queryCount++;
		self::$lastQuery = $query;
		self::$allQueries[] = $query;

		if( $returnAffected )
			$Res = self::$PDO->exec($query);
		else
			$Res = self::$PDO->query($query);

		if( $Res === false )
		{
			$err = self::$PDO->errorInfo();
			throw new DBException('Query Error on "' . $query . '"; ' . $err[2]);
		}

		return $Res;
	}

	public static function transactionCommit()
	{
		return self::$PDO->commit();
	}

	public static function transactionRollback()
	{
		return self::$PDO->rollback();
	}

	public static function transactionStart()
	{
		return self::$PDO->beginTransaction();
	}
}

try
{
	DB::addPDO(new \PDO(ASENINE_PDO_DSN, ASENINE_PDO_USER, ASENINE_PDO_PASS));
}
catch(\Exception $e)
{
	die( DEBUG ? sprintf('Database Initialization Failed with DSN %s, Reason: %s', ASENINE_PDO_DSN, $e->getMessage()) : 'System Failure');
}
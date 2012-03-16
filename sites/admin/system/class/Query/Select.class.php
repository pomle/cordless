<?
namespace Query;

class Select
{
	public function __construct($baseStatement)
	{
		$this->enforceLimit = false;
		$this->baseStatement = $baseStatement;
		$this->where = $this->group = $this->having = $this->order = array();
	}

	public function __toString()
	{
		$query = $this->baseStatement;

		if( count($this->where) > 0 )
			$query .= ' WHERE ' . join(' AND ', $this->where);


		if( count($this->group) > 0 )
			$query .= ' GROUP BY ' . join(', ', $this->group);


		if( count($this->having) > 0 )
			$query .= ' HAVING ' . join(' AND ', $this->having);


		if( count($this->order) > 0 )
			$query .= ' ORDER BY ' . join(', ', $this->order);


		if( isset($this->limit) || $this->enforceLimit !== false )
		{
			$limit = min($this->limit, $this->enforceLimit ?: $this->limit);
			$query .= sprintf(' LIMIT %u,%u', $this->offset, $limit);
		}

		return $query;
	}


	public function addWhere()
	{
		$this->where[] = call_user_func_array(array('\\Asenine\\DB', 'prepareQuery'), func_get_args());
		return $this;
	}

	public function addGroup($groupBy)
	{
		$this->group[] = $groupBy;
		return $this;
	}

	public function addHaving()
	{
		$this->having[] = call_user_func_array(array('\\Asenine\\DB', 'prepareQuery'), func_get_args());
		return $this;
	}

	public function addOrder($field, $isDescending = false, $isCollating = false)
	{
		$this->order[] = \Asenine\DB::prepareQuery(sprintf($isCollating ? '%s COLLATE || %s' : '%s %s', $field, $isDescending ? 'DESC' : 'ASC'));
		return $this;
	}

	public function setLimit($offset, $limit = null)
	{
		$this->offset = (int)$offset;
		$this->limit = (int)$limit;
		return $this;
	}
}

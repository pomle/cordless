<?
namespace Asenine\Geometry;

class Cuboid
{
	protected
		$x,
		$y,
		$z,
		$volume;


	public static function getBounds(Array $boxs)
	{
		### Returns the minimum box that will contain all $boxs

		$x = $y = $z = array();
		foreach($boxs as $Box)
		{
			if( !$Box instanceof self )
			{
				trigger_error(__METHOD__ . ' expects array of ' . __CLASS__ . ' only', E_USER_WARNING);
				return false;
			}

			$x[] = $Box->x;
			$y[] = $Box->y;
			$z[] = $Box->z;
		}

		return new self(max($x), max($y), max($z));
	}


	public function __construct($x, $y, $z)
	{
		### Always go from largest to smalles
		$xyz = array((float)abs($x), (float)abs($y), (float)abs($z));
		rsort($xyz);

		$this->x = $xyz[0];
		$this->y = $xyz[1];
		$this->z = $xyz[2];

		$this->volume = ($this->x * $this->y * $this->z);
	}

	public function __get($key)
	{
		if( $key == 'dimensions' )
		{
			return array
			(
				'x' => $this->x,
				'y' => $this->y,
				'z' => $this->z
			);
		}

		return $this->$key;
	}


	public function canEngulf(self $Box)
	{
		foreach(array('x', 'y', 'z') as $dim)
			if( $Box->$dim > $this->$dim ) return false;

		return true;
	}

	public function getLitres()
	{
		return $this->volume * 1000;
	}

	public function getSurfaceArea()
	{
		return (2 * $this->x * $this->y) + (2 * $this->y * $this->z) + (2 * $this->x * $this->z);
	}

	public function hasSpace($volume, $lengthX = null, $lengthY = null, $lengthZ = null)
	{
		if( $volume > $this->volume ) return false;

		if( !is_null($lengthX) || !is_null($lengthX) || !is_null($lengthX) )
		{
			$ourSize = array($this->x, $this->y, $this->z);
			rsort($ourSize);

			$theirSize = array((float)$lengthX, (float)$lengthY, (float)$lengthZ);
			rsort($theirSize);

			### Every dimension must fit
			foreach($ourSize as $index => $length)
				if( $theirSize[$index] > $length ) return false;
		}

		return true;
	}
}
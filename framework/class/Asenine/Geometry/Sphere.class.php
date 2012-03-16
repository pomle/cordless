<?
namespace Asenine\Geometry;

class Sphere
{
	protected
		$diameter,
		$radius,
		$volume;


	public function __construct($diameter)
	{
		$this->diameter = (float)$diameter;
		$this->radius = $this->diameter / 2;
		$this->volume = (4/3) * pi() * pow($this->radius, 3);
	}

	public function __get($key)
	{
		return $this->$key;
	}


	public function getLitres()
	{
		return $this->volume * 1000;
	}

	public function getSurfaceArea()
	{
		return 4 * pi() * pow($this->radius, 2);
	}
}
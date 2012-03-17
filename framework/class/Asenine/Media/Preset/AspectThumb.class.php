<?
namespace Asenine\Media\Preset;

class AspectThumb extends Thumb
{
	const NAME = 'aspectThumb';

	public function __construct($mediaHash, $x, $y)
	{
		parent::__construct($mediaHash, $x, $y, false);
		$this->subPath = sprintf('%ux%u/', $this->x, $this->y);
	}
}
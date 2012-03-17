<?
namespace Asenine\Media\Preset;

class CroppedThumb extends Thumb
{
	const NAME = 'croppedThumb';

	public function __construct($mediaHash, $x, $y)
	{
		parent::__construct($mediaHash, $x, $y, true);
		$this->subPath = sprintf('%ux%u/', $this->x, $this->y);
	}
}
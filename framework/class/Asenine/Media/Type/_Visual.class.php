<?
namespace Asenine\Media\Type;

interface iVisual
{
	public function getFrameCount();
	public function getFrame($index);
	public function getPreviewImage();
}

abstract class _Visual extends \Asenine\Media implements iVisual
{
	const VARIANT = 'visual';

	public function __construct($mediaHash = null, \Asenine\File $File = null)
	{
		parent::__construct($mediaHash, $File);
		$this->orientation = 0;
	}
}
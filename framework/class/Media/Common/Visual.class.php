<?
namespace Media\Common;

interface iVisual
{
	public function getFrameCount();
	public function getFrame($index);
	public function getPreviewImage();
}

abstract class Visual extends \Media implements iVisual
{
	const VARIANT = 'visual';

	public function __construct($mediaHash = null, \File $File = null)
	{
		parent::__construct($mediaHash, $File);
		$this->orientation = 0;
	}
}
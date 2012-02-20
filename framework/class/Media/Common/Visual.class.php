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

	public function __construct($mediaHash = null, $filePath = null)
	{
		parent::__construct($mediaHash, $filePath);
		$this->orientation = 0;
	}
}
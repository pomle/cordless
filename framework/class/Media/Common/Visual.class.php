<?
namespace Media\Common;

interface _Visual
{
	public function getFrameCount();
	public function getFrame($index);
	public function getPreviewImage();
}

abstract class Visual extends _Root implements _Visual
{
	const VARIANT = 'visual';
}
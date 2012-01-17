<?
namespace Media\Common;

interface iAudible
{}

abstract class Audible extends \Media implements iAudible
{
	const VARIANT = 'audible';
}
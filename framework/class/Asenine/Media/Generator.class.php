<?
namespace Asenine\Media;

interface iGenerator
{
	public function saveToFile($filePath);
}

abstract class Generator implements iGenerator
{}
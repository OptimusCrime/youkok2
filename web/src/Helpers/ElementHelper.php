<?php
namespace Youkok\Helpers;

use Youkok\Models\Element;

class ElementHelper
{
    public static function shouldDisplay(Element $element, array $fileTypes)
    {
        $ext = pathinfo($element->checksum, \PATHINFO_EXTENSION);
        return in_array($ext, $fileTypes);
    }

    public static function fileExists(Element $element, $basePath)
    {
        return file_exists(static::getPhysicalFileLocation($element, $basePath));
    }

    public static function getPhysicalFileLocation(Element $element, $basePath)
    {
        $checksum = $element->checksum;
        $folder1 = substr($checksum, 0, 1);
        $folder2 = substr($checksum, 1, 1);

        return $basePath . $folder1 . DIRECTORY_SEPARATOR . $folder2 . DIRECTORY_SEPARATOR . $checksum;
    }
}

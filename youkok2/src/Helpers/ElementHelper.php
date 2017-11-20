<?php
namespace Youkok\Helpers;

use Youkok\Models\Element;
use Youkok\Utilities\UriCleaner;

class ElementHelper
{
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

    public static function constructUri($id)
    {
        $element = Element::fromIdAll($id, ['id', 'link', 'slug', 'parent']);
        if ($element === null) {
            return null;
        }

        if ($element->isLink()) {
            return $element->link;
        }

        $fragments = [$element->slug];
        $currentParent = $element->parent;
        do {
            // Get the parent object
            $parent = Element::select('id', 'parent', 'slug', 'uri')
                ->find($currentParent);

            // If we have no valid parent object anyway we have no option but to quit (LOG ERROR)
            if ($parent === null) {
                break;
            }

            // If our parent object has their uri we can just reuse its uri
            if ($parent->uri !== null and strlen($parent->uri) > 0) {
                $fragments[] = $parent->uri;
                break;
            }

            // Just grab the slug and update parent
            $fragments[] = $parent->slug;
            $currentParent = $parent->parent;
        } while ($currentParent !== 0 and $currentParent !== null);

        // Filter the fragments
        $cleanFragments = UriCleaner::cleanFragments($fragments);

        return implode('/', array_reverse($cleanFragments));
    }
}

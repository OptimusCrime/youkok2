<?php
namespace Youkok\Processors;

use Youkok\Models\Element;

class ArchiveElementFetchProcessor
{

    public static function fromElement(Element $element)
    {
        return [
            'SITE_DESCRIPTION' => static::getSiteDescription($element),
            'ID' => $element->id,
            'ROOT_ID' => $element->rootParent->id,
            'PARENTS' => $element->parents,
            'CHILDREN' => static::getArchiveChildren($element),
            'TITLES' => static::getArchiveTitles($element),
            'SITE_TITLE' => static::getSiteTitle($element)
        ];
    }

    private static function getArchiveTitles(Element $element)
    {
        if ($element->parent === null) {
            return [
                'PRIMARY' => $element->courseCode,
                'SECONDARY' => $element->courseName
            ];
        }

        return [
            'PRIMARY' => $element->name,
            'SECONDARY' => null
        ];
    }

    private static function getSiteTitle(Element $element)
    {
        if ($element->parent === null) {
            return $element->courseCode . ' :: ' . $element->courseName;
        }

        return $element->name;
    }

    private static function getSiteDescription(Element $element)
    {
        $rootParent = $element->rootParent;
        if ($rootParent === null) {
            return '';
        }

        return $rootParent->courseCode . ' - ' .
                $rootParent->courseName . ': ' .
                'Øvinger, løsningsforslag, gamle eksamensoppgaver ' .
                'og andre ressurser på Youkok2.com, den beste kokeboka på nettet.';
    }

    private static function getArchiveChildren(Element $element)
    {
        if ($element->empty) {
            return [];
        }

        $children = Element::select('id', 'name', 'slug', 'uri', 'parent', 'empty', 'directory', 'link', 'checksum')
            ->where('parent', $element->id)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->orderBy('directory', 'DESC')
            ->orderBy('name', 'ASC')
            ->get();

        // Guard for empty set of query
        if (count($children) > 0) {
            return $children;
        }

        return [];
    }
}

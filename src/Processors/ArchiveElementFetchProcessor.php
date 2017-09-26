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
            'PARENTS' => $element->parents,
            'CHILDREN' => static::getArchiveChildren($element),
            'TITLE' => 'TODO',
            'SUB_TITLE' => 'TODO2'
        ];
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

    /*
    private function setArchiveData()
    {
        $this->setTemplateData('ARCHIVE_ID', $this->element->id);
        $this->setTemplateData('ARCHIVE_SUB_TITLE', null);

        // Load empty
        $this->setTemplateData('ARCHIVE_CHILDREN', $this->getArchiveChildren());

        // Title
        if ($this->element->parent === null) {
            $this->setTemplateData('ARCHIVE_TITLE', $this->element->courseCode);
            $this->setTemplateData('ARCHIVE_SUB_TITLE', $this->element->courseName);
            $this->setTemplateData('SITE_TITLE', $this->element->courseCode . ' :: ' . $this->element->courseName);
        }
        else {
            $this->setTemplateData('ARCHIVE_TITLE', $this->element->name);
            $this->setTemplateData('SITE_TITLE', $this->element->name);
        }
    }*/

    private static function getArchiveChildren(Element $element)
    {
        if ($element->empty) {
            return [];
        }

        $children = Element::select('id', 'name', 'slug', 'uri', 'parent', 'empty', 'directory', 'link')
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

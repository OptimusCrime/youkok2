<?php
namespace Youkok\Processors;

use Illuminate\Database\Capsule\Manager as DB;

use Youkok\Controllers\ElementController;
use Youkok\Helpers\SessionHandler;
use Youkok\Models\Download;
use Youkok\Models\Element;

class ArchiveElementFetchProcessor extends AbstractElementFactoryProcessor
{
    public static function fromElement(Element $element)
    {
        return new ArchiveElementFetchProcessor($element);
    }

    public function run()
    {
        return [
            'SITE_DESCRIPTION' => static::getSiteDescription($this->element),
            'ID' => $this->element->id,
            'ROOT_ID' => $this->element->rootParent->id,
            'PARENTS' => $this->element->parents,
            'CHILDREN' => static::getArchiveChildren($this->element),
            'TITLES' => static::getArchiveTitles($this->element),
            'SITE_TITLE' => static::getSiteTitle($this->element),
            'STARRED' => static::currentElementIsStarred($this->element, $this->sessionHandler)
        ];
    }

    private static function currentElementIsStarred(Element $element, SessionHandler $sessionHandler)
    {
        $favorites = $sessionHandler->getDataWithKey('favorites');
        if ($favorites === null or empty($favorites)) {
            return false;
        }

        return in_array($element->id, $favorites);
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

        $children = ElementController::getVisibleChildren($element->id);

        // Guard for empty set of query
        if (count($children) > 0) {
            return static::getArchiveChildrenDownloadCount($children);
        }

        return [];
    }

    private static function getArchiveChildrenDownloadCount($children)
    {
        $newChildren = [];

        foreach ($children as $child) {
            $newChild = clone $child;

            // TODO: attempt to fetch cache here

            $downloads = Download::select(DB::raw("COUNT(`id`) as `result`"))
                ->where('resource', $child->id)
                ->count();

            // TODO: cache the number of downloads here

            $newChild->_downloads = $downloads;

            $newChildren[] = $newChild;
        }

        return $newChildren;
    }
}

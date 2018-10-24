<?php
namespace Youkok\Biz;

use Illuminate\Database\Capsule\Manager as DB;

use Youkok\Controllers\ElementController;
use Youkok\Biz\Services\SessionService;
use Youkok\Models\Download;
use Youkok\Models\Element;
use Youkok\Utilities\CacheKeyGenerator;

class ArchiveElementFetchProcessor
{
    private $settings;
    private $cache;
    private $sessionHandler;
    private $element;

    public static function fromElement(Element $element)
    {
        return new ArchiveElementFetchProcessor($element);
    }

    public function withSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }

    public function run()
    {
        return [
            'SITE_DESCRIPTION' => static::getSiteDescription($this->element),
            'ID' => $this->element->id,
            'ROOT_ID' => $this->element->rootParent->id,
            'PARENTS' => $this->element->parents,
            'CHILDREN' => static::getArchiveChildren($this->element, $this->cache),
            'TITLES' => static::getArchiveTitles($this->element),
            'SITE_TITLE' => static::getSiteTitle($this->element),
            'STARRED' => static::currentElementIsStarred($this->element, $this->sessionHandler),
            'FILE_TYPES' => static::listAcceptedFileTypes($this->settings->get('file_endings'))
        ];
    }

    private static function currentElementIsStarred(Element $element, SessionService $sessionHandler)
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

    private static function getArchiveChildren(Element $element, $cache)
    {
        if ($element->empty) {
            return [];
        }

        $children = ElementController::getVisibleChildren($element->id);

        // Guard for empty set of query
        if (count($children) > 0) {
            return static::getArchiveChildrenDownloadCount($children, $cache);
        }

        return [];
    }

    private static function getArchiveChildrenDownloadCount($children, $cache)
    {
        $newChildren = [];

        foreach ($children as $child) {
            $newChild = clone $child;
            if ($child->directory === 0) {
                $downloads = static::getDownloadsFromCache($newChild, $cache);
                if ($downloads === null) {
                    $downloads = static::getDownloadsFromDatabase($newChild, $cache);
                }

                $newChild->_downloads = $downloads;
            }

            $newChildren[] = $newChild;
        }

        return $newChildren;
    }

    private static function getDownloadsFromCache(Element $element, $cache)
    {
        if ($cache === null) {
            return null;
        }

        $downloads = $cache->get(CacheKeyGenerator::keyForElementDownloads($element->id));
        if ($downloads === false) {
            return null;
        }

        return $downloads;
    }

    private static function getDownloadsFromDatabase(Element $element, $cache)
    {
        $downloads = Download::select(DB::raw("COUNT(`id`) as `result`"))
            ->where('resource', $element->id)
            ->count();

        if ($cache !== null) {
            $cache->set(CacheKeyGenerator::keyForElementDownloads($element->id), $downloads);
        }

        return $downloads;
    }

    private static function listAcceptedFileTypes(array $fileTypes)
    {
        $fileTypesFormatted = [];
        foreach ($fileTypes as $fileType) {
            $fileTypesFormatted[] = '.' . $fileType;
        }

        return implode(', ', $fileTypesFormatted);
    }
}

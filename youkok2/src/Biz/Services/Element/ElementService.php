<?php
namespace Youkok\Biz\Services\Element;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\CacheService;
use Youkok\Common\Models\Element;

class ElementService
{
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getNonDirectoryFromUri($uri)
    {
        return $this->getAnyFromUri($uri, Element::NON_DIRECTORY);
    }

    public function getDirectoryFromUri($uri)
    {
        return $this->getAnyFromUri($uri, Element::DIRECTORY);
    }

    public function getParentForElement(Element $element)
    {
        $parent = Element
            ::where('id', $element->parent)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->first();

        if (!($parent instanceof Element)) {
            throw new ElementNotFoundException();
        }

        return $parent;
    }

    public function getNumberOfVisibleFiles()
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->count();
    }

    public function getNumberOfFilesThisMonth()
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->whereDate('added', '>=', Carbon::now()->subMonth())
            ->count();
    }

    public function getLatestElements($limit = 10)
    {
        return Element::where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function updateRootElementVisited(Element $element)
    {
        $rootParent = $element->rootParent;
        if ($rootParent === null) {
            return null;
        }

        $rootParent->last_visited = Carbon::now();
        $rootParent->save();
    }

    private function getAnyFromUri($uri, $type)
    {
        $cleanUri = preg_replace("/[^A-Za-z0-9 ]/", '', $uri);

        if ($cleanUri === null || strlen($cleanUri) === 0) {
            throw new ElementNotFoundException();
        }

        $element = null;
        if ($type === Element::DIRECTORY) {
            $element = Element::fromUriDirectoryVisible($uri);
        }
        else {
            $element = Element::fromUriFileVisible($uri);
        }

        if ($element === null) {
            throw new ElementNotFoundException();
        }

        return $element;
    }
}
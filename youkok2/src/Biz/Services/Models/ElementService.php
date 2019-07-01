<?php

namespace Youkok\Biz\Services\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Collection;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\CacheService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;

// TODO: rydd i rekkefølge
class ElementService
{
    const SORT_TYPE_ORGANIZED = 0;
    const SORT_TYPE_AGE = 1;

    /** @var CacheService */
    private $cacheService;

    public function __construct($cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getNonDirectoryFromUri(string $uri): Element
    {

        return static::getAnyFromUri($uri, Element::NON_DIRECTORY);
    }

    public function getDirectoryFromUri(string $uri): Element
    {
        $key = CacheKeyGenerator::keyForVisibleUriDirectory($uri);
        $value = $this->cacheService->get($key);

        if ($value === null) {
            $element = static::getAnyFromUri($uri, Element::DIRECTORY);

            $this->cacheService->set($key, $element->id);

            return $element;
        }

        return Element::fromIdDirectoryVisible((int) $value, Element::DEFAULT_DIRECTORY_ATTRIBUTES);
    }

    public static function getParentForElement(Element $element): Element
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

    public static function getNumberOfVisibleFiles(): int
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->count();
    }

    public static function getNumberOfFilesThisMonth(): int
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->whereDate('added', '>=', Carbon::now()->subMonth())
            ->count();
    }

    public static function getLatestElements(int $limit = 10): Collection
    {
        return Element::where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function updateRootElementVisited(Element $element): void
    {
        $rootParent = $element->getRootParentVisible();
        if ($rootParent === null) {
            throw new ElementNotFoundException();
        }

        $rootParent->last_visited = Carbon::now();
        $rootParent->save();
    }

    private static function getAnyFromUri(string $uri, string $type): Element
    {
        $cleanUri = preg_replace("/[^A-Za-z0-9 ]/", '', $uri);

        if ($cleanUri === null || strlen($cleanUri) === 0) {
            throw new ElementNotFoundException();
        }

        if ($type === Element::DIRECTORY) {
            return Element::fromUriDirectoryVisible($uri);
        }

        return Element::fromUriFileVisible($uri);
    }

    public static function getAllPending(): int
    {
        return Element::where('pending', 1)
            ->where('deleted', 0)
            ->whereNotNull('parent')
            ->orderBy('name')
            ->count();
    }

    public static function getAllCourses(): Collection
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('name')
            ->get();
    }

    public static function getAllNoneEmptyCourses(): Collection
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'deleted', 'pending')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('empty', 0)
            ->orderBy('name')
            ->get();
    }

    public static function getLatest(int $limit = 10): Collection
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'added', 'checksum')
            ->where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->limit($limit)
            ->get();
    }

    // TODO: used by admin stuff
    public static function getAllChildren(int $id): Collection
    {
        return Element::select([
            'id', 'name', 'slug', 'uri', 'parent', 'empty',
            'directory', 'link', 'checksum',
            'added', 'deleted'
        ])
            ->where('parent', $id)
            ->where('pending', 0)
            ->orderBy('deleted', 'ASC')
            ->orderBy('directory', 'DESC')
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getVisibleChildren(int $id, int $order = self::SORT_TYPE_ORGANIZED): Collection
    {
        $query = Element::where('parent', $id)
            ->where('deleted', 0)
            ->where('pending', 0);

        if ($order === static::SORT_TYPE_ORGANIZED) {
            $query = $query->orderBy('directory', 'DESC')->orderBy('name', 'ASC');
        } else {
            $query = $query->orderBy('added', 'DESC');
        }

        return $query->get();
    }
}

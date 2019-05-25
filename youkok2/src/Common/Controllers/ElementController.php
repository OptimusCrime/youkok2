<?php
namespace Youkok\Common\Controllers;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Common\Models\Element;

class ElementController
{
    const SORT_TYPE_ORGANIZED = 0;
    const SORT_TYPE_AGE = 1;

    public static function getNonDirectoryFromUri(string $uri)
    {
        return static::getAnyFromUri($uri, Element::NON_DIRECTORY);
    }

    public static function getDirectoryFromUri(string $uri): Element
    {
        return static::getAnyFromUri($uri, Element::DIRECTORY);
    }

    public static function getParentForElement(Element $element)
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

    public static function getNumberOfVisibleFiles()
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->count();
    }

    public static function getNumberOfFilesThisMonth()
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->whereDate('added', '>=', Carbon::now()->subMonth())
            ->count();
    }

    public static function getLatestElements($limit = 10)
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

    public static function getAllPending()
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'deleted', 'directory')
            ->where('pending', 1)
            ->where('deleted', 0)
            ->whereNotNull('parent')
            ->orderBy('name')
            ->get();
    }

    public static function getElementsFromSearch($search = null)
    {
        if ($search === null or count($search) === 0) {
            return [];
        }

        $query = Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0);

        $query->where(function ($query) use ($search) {
            foreach ($search as $v) {
                $query->orWhere('name', 'LIKE', $v);
            }
        });

        $query->orderBy('empty');
        $query->orderBy('name');

        return $query->get();
    }

    public static function getAllCourses()
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('name')
            ->get();
    }

    public static function getAllNoneEmptyCourses()
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'deleted', 'pending')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('empty', 0)
            ->orderBy('name')
            ->get();
    }

    public static function getLatest($limit = 10)
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

    public static function getAllElements($columns = ['id', 'parent'])
    {
        return Element::select($columns)->get();
    }

    public static function getAllChildren($id)
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

    public static function getVisibleChildren($id, $order = self::SORT_TYPE_ORGANIZED)
    {
        $query = Element::select([
            'id', 'name', 'slug', 'uri', 'parent', 'empty',
            'directory', 'link',
            'checksum', 'added'
        ])
            ->where('parent', $id)
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

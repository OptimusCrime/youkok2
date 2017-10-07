<?php
namespace Youkok\Controllers;

use Youkok\Models\Element;

class ElementController
{
    const SORT_TYPE_ORGANIZED = 0;
    const SORT_TYPE_AGE = 1;

    // TODO add parameter to fetch whatever attributes we'd like for all methods
    // builder pattern?

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

    public static function getMostPopularCoursesFromDelta($limit = 10, $delta)
    {
        // TODO
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'added')
            ->where('directory', 1)
            ->where('parent', null)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function getLastVisitedCourses($limit = 10)
    {
        return Element::select('id', 'name', 'slug', 'uri', 'last_visited')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('last_visited', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function getMostPopularElementsFromDelta($limit = 10, $delta)
    {
        // TODO
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'added')
            ->where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->limit($limit)
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

    public static function getVisibleChildren($id, $order = self::SORT_TYPE_ORGANIZED)
    {
        $query = Element::select('id', 'name', 'slug', 'uri', 'parent', 'empty', 'directory', 'link', 'checksum', 'added')
            ->where('parent', $id)
            ->where('deleted', 0)
            ->where('pending', 0);

        if ($order === static::SORT_TYPE_ORGANIZED) {
            $query = $query->orderBy('directory', 'DESC')->orderBy('name', 'ASC');
        }
        else {
            $query = $query->orderBy('added', 'DESC');
        }

        return $query->get();
    }
}

<?php
namespace Youkok\Controllers;

use Youkok\Models\Element;

class ElementController
{
    const SORT_TYPE_ORGANIZED = 0;
    const SORT_TYPE_AGE = 1;

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

    public static function getMostPopularCoursesFromDelta($delta)
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'added')
            ->where('directory', 1)
            ->where('parent', null)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->get();
    }

    public static function getLastVisitedCourses()
    {
        return Element::select('id', 'name', 'slug', 'uri')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('last_visited', 'DESC')
            ->get();
    }

    public static function getMostPopularElementsFromDelta($delta)
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'added')
            ->where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->get();
    }

    public static function getLatest($limit = 10)
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'added')
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

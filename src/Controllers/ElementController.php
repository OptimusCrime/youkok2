<?php
namespace Youkok\Controllers;

use Youkok\Models\Element;

class ElementController
{
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
}

<?php
namespace Youkok\Common\Controllers;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Common\Models\Element;

class CourseController
{
    public static function getNumberOfNonVisibleCourses()
    {
        return Element
            ::where('directory', 1)
            ->where('parent', null)
            ->where('deleted', 0)
            ->where('empty', 0)
            ->count();
    }

    public static function getAllVisibleCourses()
    {
        return Element::select('id', 'name', 'slug')
            ->where('parent', null)
            ->where('directory', 1)
            ->orderBy('name')
            ->get();
    }

    public static function getCourseFromId($id)
    {
        return static::getCourseFromElement(Element::fromIdVisible($id));
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

    public static function getCourseFromElement(Element $element)
    {
        if ($element->parent === 0) {
            // TODO log
            throw new ElementNotFoundException();
        }

        $currentObject = $element;
        while ($currentObject->parent !== 0 && $currentObject->parent !== null) {
            $currentObject = Element::select('id', 'parent')
                ->where('id', $currentObject->parent)
                ->where('deleted', 0)
                ->where('pending', 0)
                ->where('directory', 1)
                ->first();

            if ($currentObject === null) {
                // TODO log
                throw new ElementNotFoundException();
            }
        }

        return Element::fromIdVisible($currentObject->id, ['id', 'name', 'slug']);
    }

    public static function getCourseFromUri($uri)
    {
        $element = Element::where('slug', $uri)
            ->where('parent', null)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->first();

        if ($element === null) {
            throw new ElementNotFoundException();
        }

        return $element;
    }
}
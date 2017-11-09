<?php
namespace Youkok\Processors\Admin;

use Youkok\Controllers\ElementController;
use Youkok\Models\Element;

class ElementListProcessor
{
    public static function run()
    {
        $courses = ElementController::getAllNoneEmptyCourses();
        foreach ($courses as $course) {
            static::fetchChildren($course);
        }

        return $courses;
    }

    public static function fetchChildrenForId($id)
    {
        $element = Element::fromIdAll($id, ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'deleted', 'pending']);
        if ($element === null) {
            return null;
        }

        static::fetchChildren($element);

        return $element;
    }

    public static function fetchChildren(Element $element)
    {
        $children = ElementController::getAllChildren($element->id);
        if (count($children) > 0) {
            $element->childrenObjects = $children;
            foreach ($element->childrenObjects as $child) {
                static::fetchChildren($child);
            }
        }
    }
}

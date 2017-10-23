<?php
namespace Youkok\Processors\Admin\File;

use Youkok\Controllers\ElementController;
use Youkok\Models\Element;

class AdminFileFetchProcessor
{
    public static function run()
    {
        $courses = ElementController::getAllNoneEmptyCourses();
        foreach ($courses as $course) {
            static::fetchChildren($course);
        }

        return $courses;
    }

    private static function fetchChildren(Element $element)
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
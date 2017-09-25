<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;

class CourseListProcessor
{
    public static function get()
    {
        $collection = [];
        $courses = ElementController::getAllCourses();

        foreach ($courses as $course) {
            $letter = substr($course->courseCode, 0, 1);

            if (!isset($collection[$letter])) {
                $collection[$letter] = [];
            }

            $collection[$letter][] = $course;
        }

        return $collection;
    }
}

<?php
namespace Youkok\Biz\Services;

use Youkok\Common\Models\Element;

class CourseListService
{
    // TODO move this into courseService
    public function get()
    {
        $collection = [];

        $courses = Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('name')
            ->get();

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

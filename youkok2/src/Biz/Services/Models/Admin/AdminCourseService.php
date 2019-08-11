<?php
namespace Youkok\Biz\Services\Models\Admin;

use Youkok\Common\Models\Element;

class AdminCourseService
{
    public function getAllNonEmptyCourses(): array
    {
        $courses = Element
            ::select('id')
            ->where('directory', 1)
            ->where('empty', 0)
            ->where('parent', null)
            ->get();

        $ids = [];
        foreach ($courses as $course) {
            $ids[] = $course->id;
        }

        return $ids;
    }

    public function getAllCoursesWithPendingContent(): array
    {
        $courses = Element
            ::select('parent')
            ->distinct()
            ->where('pending', 1)
            ->get();

        $ids = [];
        foreach ($courses as $course) {
            $ids[] = $course->parent;
        }

        return $ids;
    }
}

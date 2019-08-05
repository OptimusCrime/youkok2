<?php
namespace Youkok\Biz\Services\Models\Admin;

use Youkok\Common\Models\Element;

class AdminCourseService
{
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

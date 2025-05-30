<?php
namespace Youkok\Biz\Services\Models\Admin;

use Illuminate\Support\Collection;
use Youkok\Common\Models\Element;

class AdminCourseService
{
    public function getAllCourses(): array
    {
        $courses = Element
            ::select('id')
            ->where('directory', true)
            ->where('parent', null)
            ->get();

        return $this->mapCollectionToIdArray($courses);
    }

    public function getSingleCourse(int $id): array
    {
        $courses = Element
            ::select('id')
            ->where('id', $id)
            ->get();

        return $this->mapCollectionToIdArray($courses);
    }

    public function getAllCoursesWithPendingContent(): array
    {
        $courses = Element
            ::select('parent')
            ->distinct()
            ->where('pending', true)
            ->get();

        $ids = [];
        foreach ($courses as $course) {
            $ids[] = $course->parent;
        }

        return $ids;
    }

    public function getCourseDirectoriesTree(Element $element): Element
    {
        $childrenCollection = Element
            ::select('id', 'name', 'parent', 'link', 'directory')
            ->where('parent', $element->id)
            ->where('directory', true)
            ->where('deleted', false)
            ->where('pending', false)
            ->get();

        if (count($childrenCollection) === 0) {
            $element->setChildren([]);
            return $element;
        }

        $children = [];
        foreach ($childrenCollection as $child) {
            $children[] = $child;
        }
        $element->setChildren($children);

        foreach ($element->getChildren() as $child) {
            $this->getCourseDirectoriesTree($child);
        }

        return $element;
    }

    private function mapCollectionToIdArray(Collection $collection): array
    {
        $ids = [];
        foreach ($collection as $course) {
            $ids[] = $course->id;
        }

        return $ids;
    }
}

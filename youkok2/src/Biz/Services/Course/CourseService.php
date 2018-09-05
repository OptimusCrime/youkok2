<?php
namespace Youkok\Biz\Services\Course;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Common\Models\Element;

class CourseService
{
    public function getNumberOfNonVisibleCourses()
    {
        return Element
            ::where('directory', 1)
            ->where('parent', null)
            ->where('deleted', 0)
            ->where('empty', 0)
            ->count();
    }

    public function getAllVisibleCourses()
    {
        return Element::select('id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'deleted', 'pending')
            ->where('parent', null)
            ->where('directory', 1)
            ->where('empty', 0)
            ->orderBy('name')
            ->limit(24)
            ->get();
    }

    public function getCourseFromId($id)
    {
        return  $this->getCourseFromElement(Element::fromIdVisible($id));
    }

    public function getCourseFromElement(Element $element)
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

        return Element::fromIdVisible($currentObject->id, ['id', 'name']);
    }
}
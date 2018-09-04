<?php
namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Common\Models\Element;

class ArchiveService
{
    public function get($course, $params)
    {
        if ($params === null) {
            return $this->getCourse($course);
        }

        return null;
    }

    // TODO put this into courseService
    private function getCourse($course)
    {
        $element = Element::courseFromUriVisible($course);

        if ($element === null) {
            throw new ElementNotFoundException();
        }

        $this->updateCourseLastVisited($element);

        return $element;
    }

    // TODO put this into course service (from elementService?)
    private function updateCourseLastVisited(Element $element)
    {

    }
}
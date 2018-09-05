<?php
namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Course\CourseService;
use Youkok\Common\Models\Element;

class ArchiveService
{
    private $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function get($id)
    {
        // This method will throw an exception if the course is not found (or invisible)
        $directory = Element::fromIdVisible($id, Element::ATTRIBUTES_ALL);

        $course = $this->courseService->getCourseFromId($id);
        $content = $this->getContentForDirectory($directory);

        return [
            'course' => $course,
            'content' => $content,
        ];
    }

    public function getArchiveElementFromUri($course, $params)
    {
        if ($params === null) {
            return $this->getCourseFromUri($course);
        }

        // TODO
        return null;
    }

    private function getContentForDirectory(Element $element)
    {
        return Element
            ::where('parent', $element->id)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->orderBy('directory', 'DESC')
            ->orderBy('name', 'ASC')
            ->get();
    }

    // TODO put this into courseService
    private function getCourseFromUri($uri)
    {
        $element = Element::where('slug', $uri)
            ->where('parent', null)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->first();

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
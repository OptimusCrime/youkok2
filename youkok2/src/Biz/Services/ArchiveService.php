<?php
namespace Youkok\Biz\Services;

use Youkok\Biz\Services\Course\CourseService;
use Youkok\Biz\Services\Element\ElementService;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Common\Models\Element;

class ArchiveService
{
    private $courseService;
    private $elementService;
    private $elementMapper;

    public function __construct(CourseService $courseService, ElementService $elementService, ElementMapper $elementMapper)
    {
        $this->courseService = $courseService;
        $this->elementService = $elementService;
        $this->elementMapper = $elementMapper;
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
        $element = null;
        if ($params === null) {
            $element = $this->courseService->getCourseFromUri($course);
        }
        else {
            $element = $this->elementService->getDirectoryFromUri($course . '/' . $params);
        }

        $this->elementService->updateRootElementVisited($element);

        return $element;
    }

    public function getBreadcrumbsForElement(Element $element)
    {
        return $this->elementMapper->mapBreadcrumbs($element->parents);
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
}
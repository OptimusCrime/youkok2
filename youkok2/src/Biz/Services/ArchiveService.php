<?php
namespace Youkok\Biz\Services;

use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Common\Controllers\CourseController;
use Youkok\Common\Controllers\ElementController;
use Youkok\Common\Models\Element;

class ArchiveService
{
    private $elementMapper;

    public function __construct(ElementMapper $elementMapper)
    {
        $this->elementMapper = $elementMapper;
    }

    public function get($id)
    {
        // This method will throw an exception if the course is not found (or invisible)
        $directory = Element::fromIdVisible($id, Element::ATTRIBUTES_ALL);

        $course = CourseController::getCourseFromId($id);
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
            $element = CourseController::getCourseFromUri($course);
        }
        else {
            $element = ElementController::getDirectoryFromUri($course . '/' . $params);
        }

        ElementController::updateRootElementVisited($element);

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
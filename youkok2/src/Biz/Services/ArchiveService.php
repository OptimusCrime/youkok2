<?php

namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\ElementNotFoundException;
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

    /**
     * @param string $id
     * @return array
     * @throws ElementNotFoundException
     */

    public function get(string $id): array
    {
        $directory = Element::fromIdVisible($id, Element::ATTRIBUTES_ALL);

        $course = CourseController::getCourseFromId($id);
        $content = $this->getContentForDirectory($directory);

        return [
            'course' => $course,
            'content' => $content,
        ];
    }

    /**
     * @param string $course
     * @param string|null $params
     * @return |null
     * @throws ElementNotFoundException
     */
    public function getArchiveElementFromUri(string $course, ?string $params): Element
    {
        $element = null;
        if ($params === null) {
            $element = CourseController::getCourseFromUri($course);
        } else {
            $element = ElementController::getDirectoryFromUri($course . '/' . $params);
        }

        ElementController::updateRootElementVisited($element);

        return $element;
    }

    public function getBreadcrumbsForElement(Element $element)
    {
        return $this->elementMapper->mapBreadcrumbs($element->getParentsVisible());
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

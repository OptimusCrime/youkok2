<?php

namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Common\Controllers\CourseController;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;

class ArchiveService
{
    private $elementMapper;
    private $elementService;

    public function __construct(
        ElementMapper $elementMapper,
        ElementService $elementService
    ) {
        $this->elementMapper = $elementMapper;
        $this->elementService = $elementService;
    }

    /**
     * @param int $id
     * @return array
     * @throws ElementNotFoundException
     */

    public function get(int $id): array
    {
        $directory = Element::fromIdVisible(
            $id,
            ['id', 'name', 'slug', 'uri', 'parent', 'directory']
        );

        $course = CourseController::getCourseFromId($id);
        $content = $this->getContentForDirectory($directory);

        return [
            'course' => $course,
            'content' => $content,
        ];
    }

    public function getArchiveElementFromUri(string $uri): Element
    {
        $element = $this->elementService->getDirectoryFromUri($uri);

        $this->elementService->updateRootElementVisited($element);

        return $element;
    }

    public function getBreadcrumbsForElement(Element $element)
    {
        return $this->elementMapper->mapBreadcrumbs($element->getParentsVisible());
    }

    // TODO: Place in ElementService?
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

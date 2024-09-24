<?php
namespace Youkok\Biz\Services\Admin;

use Exception;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Mappers\Admin\AdminElementMapper;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class FilesService
{
    private ElementService $elementService;
    private AdminElementMapper $adminElementMapper;

    public function __construct(ElementService $elementService, AdminElementMapper $adminElementMapper)
    {
        $this->elementService = $elementService;
        $this->adminElementMapper = $adminElementMapper;
    }

    public function buildTree(RouteParserInterface $routeParser, array $courses): array
    {
        $content = [];
        foreach ($courses as $course) {
            try {
                $content[] = $this->buildTreeFromId($routeParser, $course);
            } catch (Exception $ex) {
                // Some legacy file is not added directory on parent, keep going, this is handled in the frontend
            }
        }

        return $content;
    }

    /**
     * @throws ElementNotFoundException
     */
    public function buildTreeFromId(RouteParserInterface $routeParser, int $id): array
    {
        $course = $this->elementService->getElement(
            new SelectStatements('id', $id),
            [
                ElementService::FLAG_ENSURE_IS_COURSE
            ]
        );

        $course->setChildren($this->getAllChildrenFromParent($course->id));

        $this->fetchDirectoryContentRecursively($course);

        return $this->adminElementMapper->map($routeParser, $course);
    }

    private function getAllChildrenFromParent(int $id): array
    {
        $collection = Element
            ::select(Element::ALL_FIELDS)
            ->where('parent', $id)
            ->orderBy('directory', 'DESC')
            ->orderBy('name', 'ASC')
            ->get();

        $children = [];
        foreach ($collection as $element) {
            $children[] = $element;
        }

        return $children;
    }

    private function fetchDirectoryContentRecursively(Element $element): void
    {
        /** @var Element $child */
        foreach ($element->getChildren() as $child) {
            if ($child->getType() !== Element::DIRECTORY) {
                continue;
            }

            $child->setChildren($this->getAllChildrenFromParent($child->id));
            $this->fetchDirectoryContentRecursively($child);
        }
    }
}

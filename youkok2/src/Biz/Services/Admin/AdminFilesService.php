<?php

namespace Youkok\Biz\Services\Admin;

use Youkok\Biz\Services\Mappers\Admin\AdminElementMapper;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class AdminFilesService
{
    const LISTING_SELECT_ATTRIBUTES = [
        'id', 'name', 'link', 'pending', 'deleted', 'parent', 'directory', 'checksum', 'uri', 'slug'
    ];

    private $elementService;
    private $adminElementMapper;

    public function __construct(ElementService $elementService, AdminElementMapper $adminElementMapper)
    {
        $this->elementService = $elementService;
        $this->adminElementMapper = $adminElementMapper;
    }

    public function getAllChildrenFromParent(int $id): array
    {
        $collection = Element
            ::select(static::LISTING_SELECT_ATTRIBUTES)
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

    public function buildTreeFromId(int $id): array
    {
        $course = $this->elementService->getElement(
            new SelectStatements('id', $id),
            static::LISTING_SELECT_ATTRIBUTES,
            [
                ElementService::FLAG_ENSURE_IS_COURSE
            ]
        );

        $course->setChildren($this->getAllChildrenFromParent($course->id));

        $this->fetchDirectoryContentRecursively($course);

        return $this->adminElementMapper->map($course);
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

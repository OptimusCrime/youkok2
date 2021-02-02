<?php
namespace Youkok\Biz\Services\Admin;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidFlagCombination;
use Youkok\Biz\Services\Mappers\Admin\AdminElementMapper;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class FilesService
{
    const LISTING_SELECT_ATTRIBUTES = [
        'id', 'name', 'link', 'pending', 'deleted', 'parent', 'directory', 'checksum', 'uri', 'slug'
    ];

    private ElementService $elementService;
    private AdminElementMapper $adminElementMapper;

    public function __construct(ElementService $elementService, AdminElementMapper $adminElementMapper)
    {
        $this->elementService = $elementService;
        $this->adminElementMapper = $adminElementMapper;
    }

    /**
     * @param array $courses
     * @return array
     * @throws ElementNotFoundException
     * @throws InvalidFlagCombination
     */
    public function buildTree(array $courses): array
    {
        $content = [];
        foreach ($courses as $course) {
            try {
                $content[] = $this->buildTreeFromId($course);
            } catch (GenericYoukokException $ex) {
                // Some legacy file is not added directory on parent, keep going, this is handled in the frontend
            }
        }

        return $content;
    }

    /**
     * @param int $id
     * @return array
     * @throws GenericYoukokException
     * @throws ElementNotFoundException
     * @throws InvalidFlagCombination
     */
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

    private function getAllChildrenFromParent(int $id): array
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

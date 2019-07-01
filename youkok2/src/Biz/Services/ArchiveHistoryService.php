<?php

namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;

class ArchiveHistoryService
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
        // This throws an exception if the element is hidden
        Element::fromIdVisible($id, ['id']);

        return $this->elementMapper->mapHistory(
            $this->elementService->getVisibleChildren($id, ElementService::SORT_TYPE_AGE)
        );
    }
}

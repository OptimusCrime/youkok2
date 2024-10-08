<?php
namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Utilities\SelectStatements;

class ArchiveHistoryService
{
    private ElementMapper $elementMapper;
    private ElementService $elementService;

    public function __construct(
        ElementMapper $elementMapper,
        ElementService $elementService
    ) {
        $this->elementMapper = $elementMapper;
        $this->elementService = $elementService;
    }

    /**
     * @throws ElementNotFoundException
     */
    public function get(int $id): array
    {
        // This throws an exception if the element is hidden
         $element = $this->elementService->getElement(
             new SelectStatements('id', $id),
             [
                ElementService::FLAG_ENSURE_VISIBLE
             ]
         );

        return $this->elementMapper->mapHistory(
            $this->elementService->getVisibleChildren($element, ElementService::SORT_TYPE_AGE)
        );
    }
}

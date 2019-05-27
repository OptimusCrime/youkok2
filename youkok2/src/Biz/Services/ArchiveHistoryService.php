<?php
namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Common\Controllers\ElementController;
use Youkok\Common\Models\Element;

class ArchiveHistoryService
{
    private $elementMapper;

    public function __construct(ElementMapper $elementMapper)
    {
        $this->elementMapper = $elementMapper;
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
            ElementController::getVisibleChildren($id, ElementController::SORT_TYPE_AGE)
        );
    }
}

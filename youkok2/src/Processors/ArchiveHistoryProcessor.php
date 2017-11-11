<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;
use Youkok\Models\Element;

class ArchiveHistoryProcessor
{
    public static function run($id)
    {
        $element = Element::fromId($id);
        if ($element === null) {
            // TODO consider better handling of stuff, throw exception and catch in view?
            return [];
        }

        return ElementController::getVisibleChildren($element->id, ElementController::SORT_TYPE_AGE);
    }
}

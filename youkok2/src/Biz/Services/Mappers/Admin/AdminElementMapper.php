<?php

namespace Youkok\Biz\Services\Mappers\Admin;

use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Common\Models\Element;

class AdminElementMapper
{
    private $elementMapper;

    public function __construct(ElementMapper $elementMapper)
    {
        $this->elementMapper = $elementMapper;
    }

    public function map(Element $element): array
    {
        $arr = $this->elementMapper->mapElement(
            $element, [
                ElementMapper::ICON
            ]
        );

        $arr['deleted'] = $element->deleted;
        $arr['pending'] = $element->pending;

        if (count($element->getChildren()) !== 0) {
            $arr['children'] = [];

            foreach ($element->getChildren() as $child) {
                $arr['children'][] = $this->map($child);
            }
        }

        return $arr;
    }
}

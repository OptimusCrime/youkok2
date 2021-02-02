<?php
namespace Youkok\Biz\Pools\Containers;

use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class ElementPoolContainer
{
    private array $attributes;
    private SelectStatements $selectStatements;
    private Element $element;

    public function __construct(array $attributes, SelectStatements $selectStatements, Element $element)
    {
        $this->attributes = $attributes;
        $this->selectStatements = $selectStatements;
        $this->element = $element;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getElement(): Element
    {
        return $this->element;
    }

    public function equals(array $attributes, SelectStatements $selectStatements): bool
    {
        if ($attributes !== $this->attributes) {
            // Attributes differ, exit early
            return false;
        }

        if (!$this->selectStatements->equals($selectStatements)) {
            return false;
        }

        // Perhaps all attributes are also found in the current set of attributes?
        return count(array_intersect($attributes, $this->attributes)) === count($attributes);
    }
}

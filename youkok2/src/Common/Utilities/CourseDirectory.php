<?php
namespace Youkok\Common\Utilities;

use Youkok\Common\Models\Element;

class CourseDirectory
{
    private $id;
    private $name;
    private $selected;

    public function __construct(Element $currentElement, Element $directory, int $depth)
    {
        $this->id = $directory->id;
        $this->selected = $currentElement->parent === $directory->id;
        $this->name = static::getCourseDirectoryName($currentElement, $directory, $depth);
    }

    public function getOutput(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'selected' => $this->selected,
        ];
    }

    private static function getCourseDirectoryName(Element $currentElement, Element $directory, int $depth): string
    {
        $prefix = str_repeat('-', $depth + 1);
        $displayName = static::getCourseDirectoryDisplayName($directory);
        $suffix = $currentElement->id === $directory->id ? ' (Gjeldene element)' : '';

        return $prefix
            . ' '
            . $displayName
            . $suffix;
    }

    private static function getCourseDirectoryDisplayName(Element $directory): string
    {
        if ($directory->getType() === Element::COURSE) {
            return $directory->getCourseCode() . ': ' . $directory->getCourseName();
        }

        return $directory->name;
    }
}

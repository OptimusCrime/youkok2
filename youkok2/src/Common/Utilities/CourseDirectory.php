<?php
namespace Youkok\Common\Utilities;

use Exception;
use Youkok\Common\Models\Element;

class CourseDirectory
{
    private int $id;
    private string $name;

    /**
     * @throws Exception
     */
    public function __construct(Element $directory, int $depth)
    {
        $this->id = $directory->id;
        $this->name = static::getCourseDirectoryName($directory, $depth);
    }

    public function getOutput(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

    /**
     * @throws Exception
     */
    private static function getCourseDirectoryName(Element $directory, int $depth): string
    {
        $prefix = str_repeat('-', $depth);
        $displayName = static::getCourseDirectoryDisplayName($directory);

        return $prefix
            . ' '
            . $displayName;
    }

    /**
     * @throws Exception
     */
    private static function getCourseDirectoryDisplayName(Element $directory): string
    {
        if ($directory->getType() === Element::COURSE) {
            return $directory->getCourseCode() . ': ' . $directory->getCourseName();
        }

        return $directory->name;
    }
}

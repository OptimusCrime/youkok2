<?php
namespace Youkok\Helpers;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Common\Models\Element;

// TODO Move into service
class ElementHelper
{
    public static function getPhysicalFileLocation(Element $element, $basePath): string
    {
        $checksum = $element->checksum;
        $folder1 = substr($checksum, 0, 1);
        $folder2 = substr($checksum, 1, 1);

        return $basePath . $folder1 . DIRECTORY_SEPARATOR . $folder2 . DIRECTORY_SEPARATOR . $checksum;
    }

    public static function siteTitleFor(Element $element): string
    {
        if ($element->isCourse()) {
            return 'Bidrag for ' . $element->getCourseCode() . ' - ' . $element->getCourseName();
        }

        $course = $element->getCourse();

        if ($course === null) {
            throw new ElementNotFoundException('No course loaded for element ' . $element->id);
        }

        return 'Bidrag i '
            . $element->name
            . ' for '
            . $course->getCourseCode()
            . ' - '
            . $course->getCourseName();
    }

    public static function siteDescriptionFor(Element $element): string
    {
        if ($element->isCourse()) {
            return 'Bidrag for '
                . $element->getCourseCode()
                . ' - '
                . $element->getCourseName()
                . ' fra Youkok2, den beste kokeboka på nettet.';
        }

        $course = $element->getCourse();

        if ($course === null) {
            throw new ElementNotFoundException('No course loaded for element ' . $element->id);
        }

        return static::siteDescriptionFor($course);
    }
}

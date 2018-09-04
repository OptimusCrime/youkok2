<?php
namespace Youkok\Biz\Services\Mappers;

use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;

class CourseMapper
{
    private $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function map($courses)
    {
        $out = [];
        foreach ($courses as $course) {
            $out[] = $this->mapCourse($course);
        }

        return $out;
    }

    public function mapCourse(Element $element)
    {
        return [
            'id' => $element->id,
            'courseCode' => $element->courseCode,
            'courseName' => $element->courseName,
            'url' => $this->urlService->urlForCourse($element),
            'type' => Element::COURSE
        ];
    }
}
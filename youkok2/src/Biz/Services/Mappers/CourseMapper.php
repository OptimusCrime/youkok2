<?php
namespace Youkok\Biz\Services\Mappers;

use Slim\Interfaces\RouterInterface;

use Youkok\Common\Models\Element;

class CourseMapper
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
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
            'url' => $this->router->pathFor('archive', ['course' => $element->courseCode]),
            'type' => Element::COURSE
        ];
    }
}
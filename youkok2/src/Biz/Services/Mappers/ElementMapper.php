<?php

namespace Youkok\Biz\Services\Mappers;


use Slim\Interfaces\RouterInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Course\CourseService;
use Youkok\Biz\Services\Element\ElementService;
use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;

class ElementMapper
{
    const PARENT_DIRECT = 'PARENT_DIRECT';
    const PARENT_COURSE = 'PARENT_COURSE';
    const POSTED_TIME = 'POSTED_TIME';
    const DOWNLOADS = 'DOWNLOADS';
    const DOWNLOADS_ESTIMATE = 'DOWNLOADS_ESTIMATE';

    private $urlService;
    private $elementService;
    private $courseService;
    private $courseMapper;


    public function __construct(
        UrlService $urlService,
        ElementService $elementService,
        CourseService $courseService,
        CourseMapper $courseMapper
    )
    {
        $this->urlService = $urlService;
        $this->elementService = $elementService;
        $this->courseService = $courseService;
        $this->courseMapper = $courseMapper;
    }

    public function map($elements, $additionalFields = [])
    {
        $out = [];
        foreach ($elements as $element) {
            $out[] = $this->mapElement($element, $additionalFields);
        }

        return $out;
    }

    public function mapElement(Element $element, $additionalFields = [])
    {
        $arr = [
            'id' => $element->id,
            'name' => $element->name,
            'type' => $element->getType(),
            'url' => $this->urlService->urlForElement($element)
        ];

        if (in_array(ElementMapper::POSTED_TIME, $additionalFields)) {
            $arr['added'] = $element->added;
        }

        if (in_array(ElementMapper::PARENT_DIRECT, $additionalFields)) {
            try {
                $parent = $this->elementService->getParentForElement($element);
                $arr['parent'] = $parent->isCourse() ? $this->courseMapper->mapCourse($parent) : $this->mapElement($parent);
            } catch (ElementNotFoundException $e) {
                // TODO log
            }

        }

        if (in_array(ElementMapper::PARENT_COURSE, $additionalFields)) {
            try {
                $course = $this->courseService->getCourseForElement($element);
                $arr['course'] = $this->courseMapper->mapCourse($course);
            } catch (ElementNotFoundException $e) {
                // TODO log
            }

        }

        return $arr;
    }

}
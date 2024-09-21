<?php
namespace Youkok\Biz\Services\Mappers\Admin;

use Exception;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;

class AdminElementMapper
{
    private ElementMapper $elementMapper;
    private UrlService $urlService;

    public function __construct(ElementMapper $elementMapper, UrlService $urlService)
    {
        $this->elementMapper = $elementMapper;
        $this->urlService = $urlService;
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function map(RouteParserInterface $routeParser, Element $element): array
    {
        $arr = $this->elementMapper->mapElement(
            $routeParser,
            $element,
            [
                ElementMapper::ICON
            ]
        );

        if ($element->getType() === Element::COURSE) {
            $arr['courseCode'] = $element->getCourseCode();
            $arr['courseName'] = $element->getCourseName();
            $arr['url'] = $this->urlService->urlForCourse($routeParser, $element);
            unset($arr['name']);
        }

        $arr['deleted'] = $element->deleted;
        $arr['pending'] = $element->pending;

        if (count($element->getChildren()) !== 0) {
            $arr['children'] = [];

            foreach ($element->getChildren() as $child) {
                $arr['children'][] = $this->map($routeParser, $child);
            }
        }

        return $arr;
    }
}

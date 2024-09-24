<?php
namespace Youkok\Biz\Services\Mappers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;

class CourseMapper
{
    const int REGULAR = 1;
    const int ADMIN = 2;

    const string DOWNLOADS_TODAY = 'DOWNLOADS_TODAY';
    const string DOWNLOADS_WEEK = 'DOWNLOADS_WEEK';
    const string DOWNLOADS_MONTH = 'DOWNLOADS_MONTH';
    const string DOWNLOADS_YEAR = 'DOWNLOADS_YEAR';
    const string DOWNLOADS_ALL = 'DOWNLOADS_ALL';

    private UrlService $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * @throws Exception
     */
    public function mapCourse(RouteParserInterface $routeParser, Element $element, array $additionalFields = []): array
    {
        $arr = [
            'id' => $element->id,
            'courseCode' => $element->getCourseCode(),
            'courseName' => $element->getCourseName(),
            'url' => $this->urlService->urlForCourse($routeParser, $element),
            'type' => Element::COURSE
        ];

        if (in_array(static::DOWNLOADS_TODAY, $additionalFields)) {
            $arr['downloads'] = $element->downloads_today;
        }

        if (in_array(static::DOWNLOADS_WEEK, $additionalFields)) {
            $arr['downloads'] = $element->downloads_week;
        }

        if (in_array(static::DOWNLOADS_MONTH, $additionalFields)) {
            $arr['downloads'] = $element->downloads_month;
        }

        if (in_array(static::DOWNLOADS_YEAR, $additionalFields)) {
            $arr['downloads'] = $element->downloads_year;
        }

        if (in_array(static::DOWNLOADS_ALL, $additionalFields)) {
            $arr['downloads'] = $element->downloads_all;
        }

        return $arr;
    }

    /**
     * @throws Exception
     */
    public function mapLastVisited(RouteParserInterface $routeParser, Collection $lastVisited): array
    {
        $out = [];

        foreach ($lastVisited as $course) {
            $out[] = $this->mapLastVisitedCourse($routeParser, $course);
        }

        return $out;
    }

    /**
     * @throws Exception
     */
    public function mapCoursesToLookup(RouteParserInterface $routeParser, Collection $courses, int $mode = CourseMapper::REGULAR): array
    {
        $output = [];
        foreach ($courses as $course) {
            if ($mode === static::REGULAR) {
                $output[] = $this->mapCoursesToLookupRegular($routeParser, $course);
            }
            else {
                $output[] = $this->mapCoursesToLookupAdmin($routeParser, $course);
            }
        }

        return $output;
    }

    /**
     * @throws Exception
     */
    private function mapLastVisitedCourse(RouteParserInterface $routeParser, Element $element): array
    {
        return [
            'id' => $element->id,
            'courseCode' => $element->getCourseCode(),
            'courseName' => $element->getCourseName(),
            'url' => $this->urlService->urlForCourse($routeParser, $element),
            'type' => Element::COURSE,
            'last_visited' => $element->last_visited,
        ];
    }

    /**
     * @throws Exception
     */
    private function mapCoursesToLookupRegular(RouteParserInterface $routeParser, Element $course): array
    {
        return array_merge(
            $this->mapCoursesToLookupBase($course), [
                'url' => $this->urlService->urlForCourse($routeParser, $course),
            ]
        );
    }

    /**
     * @throws Exception
     */
    private function mapCoursesToLookupAdmin(RouteParserInterface $routeParser, Element $course): array
    {
        return array_merge(
            $this->mapCoursesToLookupBase($course), [
                'url' => $this->urlService->urlForCourseAdmin($routeParser, $course),
            ]
        );
    }

    /**
     * @throws Exception
     */
    private function mapCoursesToLookupBase(Element $course): array
    {
        return [
            'id' => $course->id,
            'name' => $course->getCourseName(),
            'code' => $course->getCourseCode(),
            'empty' => $course->empty,
        ];
    }
}

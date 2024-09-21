<?php
namespace Youkok\Biz\Services\Mappers;

use Carbon\Carbon;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;

class CourseMapper
{
    const int REGULAR = 1;
    const int ADMIN = 2;

    const string DATASTORE_DOWNLOAD_ESTIMATE = 'DOWNLOADS_ESTIMATE';

    private UrlService $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * @throws Exception
     */
    public function map(RouteParserInterface $routeParser, $courses, array $additionalFields = []): array
    {
        $out = [];
        foreach ($courses as $course) {
            $out[] = $this->mapCourse($routeParser, $course, $additionalFields);
        }

        return $out;
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

        // This is stored in the Elements datastore (prefixed with an underscore)
        if (in_array(static::DATASTORE_DOWNLOAD_ESTIMATE, $additionalFields)) {
            $arr['download_estimate'] = $element->getDownloads();
        }

        return $arr;
    }

    public function mapLastVisited(array $lastVisited): array
    {
        $out = [];

        foreach ($lastVisited as $course) {
            $out[] = $this->mapLastVisitedCourse($course);
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

    private function mapLastVisitedCourse(array $course): array
    {
        return [
            'id' => $course['id'],
            'courseCode' => $course['courseCode'],
            'courseName' => $course['courseName'],
            'url' => $course['url'],
            'type' => Element::COURSE,
            'last_visited' => Carbon::createFromTimestamp($course['last_visited'])->format('Y-m-d H:i:s')
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
            'empty' => $course->empty === 1,
        ];
    }
}

<?php
namespace Youkok\Biz\Services\Mappers;

use Carbon\Carbon;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;

class CourseMapper
{
    const DATASTORE_DOWNLOAD_ESTIMATE = 'DOWNLOADS_ESTIMATE';

    private UrlService $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    /**
     * @param $courses
     * @param array $additionalFields
     * @return array
     * @throws GenericYoukokException
     */
    public function map($courses, $additionalFields = []): array
    {
        $out = [];
        foreach ($courses as $course) {
            $out[] = $this->mapCourse($course, $additionalFields);
        }

        return $out;
    }

    /**
     * @param Element $element
     * @param array $additionalFields
     * @return array
     * @throws GenericYoukokException
     */
    public function mapCourse(Element $element, $additionalFields = []): array
    {
        $arr = [
            'id' => $element->id,
            'courseCode' => $element->getCourseCode(),
            'courseName' => $element->getCourseName(),
            'url' => $this->urlService->urlForCourse($element),
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
}

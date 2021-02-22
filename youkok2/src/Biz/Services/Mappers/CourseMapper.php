<?php
namespace Youkok\Biz\Services\Mappers;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Collection;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;

class CourseMapper
{
    const REGULAR = 1;
    const ADMIN = 2;

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

    /**
     * @param Collection $courses
     * @param int $mode
     * @return array
     * @throws GenericYoukokException
     */
    public function mapCoursesToLookup(Collection $courses, int $mode = CourseMapper::REGULAR): array
    {
        $output = [];
        foreach ($courses as $course) {
            if ($mode === static::REGULAR) {
                $output[] = $this->mapCoursesToLookupRegular($course);
            }
            else {
                $output[] = $this->mapCoursesToLookupAdmin($course);
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
     * @param Element $course
     * @return array
     * @throws GenericYoukokException
     */
    private function mapCoursesToLookupRegular(Element $course): array
    {
        return array_merge(
            $this->mapCoursesToLookupBase($course), [
                'url' => $this->urlService->urlForCourse($course),
            ]
        );
    }

    /**
     * @param Element $course
     * @return array[]
     * @throws GenericYoukokException
     */
    private function mapCoursesToLookupAdmin(Element $course): array
    {
        return array_merge(
            $this->mapCoursesToLookupBase($course), [
                'url' => $this->urlService->urlForCourseAdmin($course),
            ]
        );
    }

    /**
     * @param Element $course
     * @return array
     * @throws GenericYoukokException
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

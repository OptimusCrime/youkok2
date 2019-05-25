<?php
namespace Youkok\Biz\Services\Mappers;

use Youkok\Biz\Services\UrlService;
use Youkok\Common\Models\Element;

class CourseMapper
{
    const LAST_VISITED = 'LAST_VISITED';
    const DATASTORE_DOWNLOAD_ESTIMATE = 'DOWNLOADS_ESTIMATE';

    private $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function map($courses, $additionalFields = [])
    {
        $out = [];
        foreach ($courses as $course) {
            $out[] = $this->mapCourse($course, $additionalFields);
        }

        return $out;
    }

    public function mapCourse(Element $element, $additionalFields = [])
    {
        $arr = [
            'id' => $element->id,
            'courseCode' => $element->getCourseCode(),
            'courseName' => $element->getCourseName(),
            'url' => $this->urlService->urlForCourse($element),
            'type' => Element::COURSE
        ];

        if (in_array(static::LAST_VISITED, $additionalFields)) {
            $arr['last_visited'] = $element->last_visited;
        }

        // This is stored in the Elements datastore (prefixed with an underscore)
        if (in_array(static::DATASTORE_DOWNLOAD_ESTIMATE, $additionalFields)) {
            $arr['download_estimate'] = $element->getDownloads();
        }

        return $arr;
    }
}

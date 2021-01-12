<?php
namespace Youkok\Biz\Services;

use Illuminate\Database\Eloquent\Collection;
use Monolog\Logger;
use Youkok\Biz\Exceptions\CacheServiceException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\IdenticalLookupException;
use Youkok\Biz\Exceptions\YoukokException;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\CoursesCacheConstants;
use Youkok\Helpers\Configuration\Configuration;

class CoursesLookupService
{
    private UrlService $urlService;
    private CourseService $courseService;
    private CacheService $cacheService;
    private Logger $logger;

    public function __construct(
        UrlService $urlService,
        CourseService $courseService,
        CacheService $cacheService,
        Logger $logger
    ) {
        $this->urlService = $urlService;
        $this->courseService = $courseService;
        $this->cacheService = $cacheService;
        $this->logger = $logger;
    }

    /**
     * @param string|null $checksum
     * @return array
     * @throws GenericYoukokException
     * @throws IdenticalLookupException
     */
    public function get(?string $checksum): array
    {
        if ($checksum === null) {
            return $this->getCoursesFromCache();
        }

        $currentChecksum = $this->getCurrentChecksum();
        if ($currentChecksum === $checksum) {
            throw new IdenticalLookupException();
        }

        // If we got here, it is because of one of these reasons:
        // - The cache checksums are different
        // - The server cache checksum is null
        // Regardless of the reason, attempt to refresh the cache.
        return $this->getCoursesFromCache();
    }

    /**
     * @return array
     * @throws GenericYoukokException
     */
    private function getCoursesFromCache(): array
    {
        $courses = $this->getCoursesData();
        if ($courses !== null) {
            return json_decode($courses, true);
        }

        // Cache is empty, try refreshing it before retrying once more
        $coursesFromQuery = $this->refreshLookupCache();

        if ($coursesFromQuery === null || (is_array($coursesFromQuery) && count($coursesFromQuery) === 0)) {
            throw new GenericYoukokException('Failed to load courses from the database for lookup');
        }

        return $coursesFromQuery;
    }

    private function refreshLookupCache(): array
    {
        $courses = $this->coursesToJsonData($this->courseService->getAllVisibleCourses());
        $coursesJson = json_encode($courses);

        $resultData = $this->cacheService->set(CacheKeyGenerator::keyForCoursesLookupData(), $coursesJson);
        $resultKey = $this->cacheService->set(CacheKeyGenerator::keyForCoursesLookupChecksum(), sha1($coursesJson));

        return $courses;
    }

    private function getCurrentChecksum(): ?string
    {
        $checksum = $this->cacheService->get(CacheKeyGenerator::keyForCoursesLookupChecksum());
    }

    private function getCoursesData(): ?string
    {
        return $this->cacheService->get(CacheKeyGenerator::keyForCoursesLookupData());
    }

    private function coursesToJsonData(Collection $courses): array
    {
        $output = [];
        foreach ($courses as $course) {
            $output[] = $this->mapCourse($course);
        }

        return $output;
    }

    private function mapCourse(Element $course): array
    {
        return [
            'id' => $course->id,
            'name' => $course->getCourseName(),
            'code' => $course->getCourseCode(),
            'url' => $this->urlService->urlForCourse($course),
            'empty' => $course->empty === 1,
        ];
    }
}

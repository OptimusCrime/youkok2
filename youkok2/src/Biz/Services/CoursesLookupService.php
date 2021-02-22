<?php
namespace Youkok\Biz\Services;

use Monolog\Logger;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\IdenticalLookupException;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Common\Utilities\CacheKeyGenerator;

class CoursesLookupService
{
    private UrlService $urlService;
    private CourseService $courseService;
    private CourseMapper $courseMapper;
    private CacheService $cacheService;
    private Logger $logger;

    public function __construct(
        UrlService $urlService,
        CourseService $courseService,
        CourseMapper $courseMapper,
        CacheService $cacheService,
        Logger $logger
    ) {
        $this->urlService = $urlService;
        $this->courseService = $courseService;
        $this->courseMapper = $courseMapper;
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
    public function getCoursesToAdminLookup(): array
    {
        return $this->courseMapper->mapCoursesToLookup(
            $this->courseService->getAllCourses(),
            CourseMapper::ADMIN,
        );
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

    /**
     * @return array
     * @throws GenericYoukokException
     */
    private function refreshLookupCache(): array
    {
        $courses = $this->courseMapper->mapCoursesToLookup($this->courseService->getAllCourses());
        $coursesJson = json_encode($courses);

        $this->cacheService->set(CacheKeyGenerator::keyForCoursesLookupData(), $coursesJson);
        $this->cacheService->set(CacheKeyGenerator::keyForCoursesLookupChecksum(), sha1($coursesJson));

        return $courses;
    }

    private function getCurrentChecksum(): ?string
    {
        return $this->cacheService->get(CacheKeyGenerator::keyForCoursesLookupChecksum());
    }

    private function getCoursesData(): ?string
    {
        return $this->cacheService->get(CacheKeyGenerator::keyForCoursesLookupData());
    }
}

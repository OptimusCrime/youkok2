<?php
namespace Youkok\Biz\Services;

use Exception;
use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\IdenticalLookupException;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Common\Utilities\CacheKeyGenerator;

class CoursesLookupService
{
    private CourseService $courseService;
    private CourseMapper $courseMapper;
    private CacheService $cacheService;

    public function __construct(
        CourseService $courseService,
        CourseMapper $courseMapper,
        CacheService $cacheService,
    ) {
        $this->courseService = $courseService;
        $this->courseMapper = $courseMapper;
        $this->cacheService = $cacheService;
    }

    /**
     * @throws IdenticalLookupException
     * @throws RedisException
     */
    public function get(RouteParserInterface $routeParser, ?string $checksum): array
    {
        if ($checksum === null) {
            return $this->getCoursesFromCache($routeParser);
        }

        $currentChecksum = $this->getCurrentChecksum();
        if ($currentChecksum === $checksum) {
            throw new IdenticalLookupException();
        }

        // If we got here, it is because of one of these reasons:
        // - The cache checksums are different
        // - The server cache checksum is null
        // Regardless of the reason, attempt to refresh the cache.
        return $this->getCoursesFromCache($routeParser);
    }

    /**
     * @throws Exception
     */
    public function getCoursesToAdminLookup(RouteParserInterface $routeParser): array
    {
        return $this->courseMapper->mapCoursesToLookup(
            $routeParser,
            $this->courseService->getAllCourses(),
            CourseMapper::ADMIN,
        );
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    private function getCoursesFromCache(RouteParserInterface $routeParser): array
    {
        $courses = $this->getCoursesData();
        if ($courses !== null) {
            return json_decode($courses, true);
        }

        // Cache is empty, try refreshing it before retrying once more
        $coursesFromQuery = $this->refreshLookupCache($routeParser);

        if (count($coursesFromQuery) === 0) {
            throw new Exception('Failed to load courses from the database for lookup');
        }

        return $coursesFromQuery;
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    private function refreshLookupCache(RouteParserInterface $routeParser): array
    {
        $courses = $this->courseMapper->mapCoursesToLookup($routeParser, $this->courseService->getAllCourses());
        $coursesJson = json_encode($courses);

        $this->cacheService->set(CacheKeyGenerator::keyForCoursesLookupData(), $coursesJson);
        $this->cacheService->set(CacheKeyGenerator::keyForCoursesLookupChecksum(), sha1($coursesJson));

        return $courses;
    }

    /**
     * @throws RedisException
     */
    private function getCurrentChecksum(): ?string
    {
        return $this->cacheService->get(CacheKeyGenerator::keyForCoursesLookupChecksum());
    }

    /**
     * @throws RedisException
     */
    private function getCoursesData(): ?string
    {
        return $this->cacheService->get(CacheKeyGenerator::keyForCoursesLookupData());
    }
}

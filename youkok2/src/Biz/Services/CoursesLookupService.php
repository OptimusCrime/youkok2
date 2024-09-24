<?php
namespace Youkok\Biz\Services;

use Exception;
use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\IdenticalLookupException;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Common\Utilities\CacheKeyGenerator;

const MILLISECONDS_IN_DAY = 60 * 60 * 24 * 1000;

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
     * @throws Exception
     */
    public function get(RouteParserInterface $routeParser, ?string $checksum): array
    {
        $keyChecksum = CacheKeyGenerator::keyForCoursesLookupChecksum();
        $currentChecksum = $this->cacheService->get($keyChecksum);

        if ($checksum !== null && $currentChecksum !== null && $currentChecksum === $checksum) {
            throw new IdenticalLookupException();
        }

        $lookupKey = CacheKeyGenerator::keyForCoursesLookupData();
        $courses = $this->cacheService->get($lookupKey);
        if ($courses !== null) {
            return json_decode($courses, true);
        }

        $courses = $this->courseMapper->mapCoursesToLookup($routeParser, $this->courseService->getAllCourses());
        $coursesJson = json_encode($courses);

        $this->cacheService->set(CacheKeyGenerator::keyForCoursesLookupData(), $coursesJson,MILLISECONDS_IN_DAY);
        $this->cacheService->set(CacheKeyGenerator::keyForCoursesLookupChecksum(), sha1($coursesJson), MILLISECONDS_IN_DAY);


        if (count($courses) === 0) {
            throw new Exception('Failed to load courses from the database for lookup');
        }

        return $courses;
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
}

<?php
namespace Youkok\Biz\Services;

use Exception;
use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Utilities\CacheKeyGenerator;

class FrontpageService
{
    const int SERVICE_LIMIT = 10;

    private CacheService $cacheService;
    private ElementService $elementService;
    private CourseService $courseService;
    private DownloadService $downloadService;
    private ElementMapper $elementMapper;
    private CourseMapper $courseMapper;

    public function __construct(
        CacheService $cacheService,
        ElementService $elementService,
        CourseService $courseService,
        DownloadService $downloadService,
        ElementMapper $elementMapper,
        CourseMapper $courseMapper
    ) {
        $this->cacheService = $cacheService;
        $this->elementService = $elementService;
        $this->courseService = $courseService;
        $this->downloadService = $downloadService;
        $this->elementMapper = $elementMapper;
        $this->courseMapper = $courseMapper;
    }

    /**
     * @throws RedisException
     */
    public function boxes(): array
    {
        $numberFiles = $this->cacheService->get(CacheKeyGenerator::keyForBoxesNumberOfFiles());
        if ($numberFiles === null) {
            $numberFiles = $this->elementService->getNumberOfVisibleFiles();

            $this->cacheService->set(CacheKeyGenerator::keyForBoxesNumberOfFiles(), (string) $numberFiles);
        }

        $numberOfDownloads = $this->cacheService->get(CacheKeyGenerator::keyForTotalNumberOfDownloads());
        if ($numberOfDownloads === null) {
            $numberOfDownloads = $this->downloadService->getNumberOfDownloads();

            $this->cacheService->set(CacheKeyGenerator::keyForTotalNumberOfDownloads(), (string) $numberOfDownloads);
        }

        $numberOfCoursesWithContent = $this->cacheService->get(
            CacheKeyGenerator::keyForBoxesNumberOfCoursesWithContent()
        );

        if ($numberOfCoursesWithContent === null) {
            $numberOfCoursesWithContent = $this->courseService->getNumberOfVisibleCourses();

            $this->cacheService->set(
                CacheKeyGenerator::keyForBoxesNumberOfCoursesWithContent(),
                (string) $numberOfCoursesWithContent
            );
        }

        $numberOfFilesThisMonth = $this->cacheService->get(CacheKeyGenerator::keyForBoxesNumberOfFilesThisMonth());
        if ($numberOfFilesThisMonth === null) {
            $numberOfFilesThisMonth = $this->elementService->getNumberOfFilesThisMonth();

            $this->cacheService->set(
                CacheKeyGenerator::keyForBoxesNumberOfFilesThisMonth(),
                (string) $numberOfFilesThisMonth
            );
        }

        return [
            'number_files' => (int) $numberFiles,
            'number_downloads' => (int) $numberOfDownloads,
            'number_courses_with_content' => (int) $numberOfCoursesWithContent,
            'number_new_elements' => (int) $numberOfFilesThisMonth,
        ];
    }

    /**
     * @throws ElementNotFoundException
     * @throws RedisException
     */
    public function getNewestElements(RouteParserInterface $routeParser): array
    {
        $cacheKey = CacheKeyGenerator::keyForNewestElementsPayload();
        $cache = $this->cacheService->get($cacheKey);

        if ($cache !== null) {
            return json_decode($cache, true);
        }

        $payload = $this->elementService->getNewestElements(static::SERVICE_LIMIT);
        $data = $this->elementMapper->mapFromArray(
            $routeParser,
            $payload,
            [
                ElementMapper::POSTED_TIME,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );

        $this->cacheService->set(
            $cacheKey,
            json_encode($data)
        );

        return $data;
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    public function getLastVisitedCurses(RouteParserInterface $routeParser): array
    {
        $cacheKey = CacheKeyGenerator::keyForLastVisitedCoursesPayload();
        $cache = $this->cacheService->get($cacheKey);

        if ($cache !== null) {
            return json_decode($cache, true);
        }

        $payload = $this->courseService->getLastVisitedCourses();

        $data = $this->courseMapper->mapLastVisited($routeParser, $payload);

        $this->cacheService->set(
            $cacheKey,
            json_encode($data)
        );

        return $data;
    }

    /**
     * @throws RedisException
     * @throws ElementNotFoundException
     */
    public function getLastDownloaded(RouteParserInterface $routeParser): array
    {
        $cacheKey = CacheKeyGenerator::keyForLastDownloadedPayload();
        $cache = $this->cacheService->get($cacheKey);

        if ($cache !== null) {
            return json_decode($cache, true);
        }

        $payload = $this->downloadService->getLatestDownloads(static::SERVICE_LIMIT);

        $data = $this->elementMapper->mapFromArray(
            $routeParser,
            $payload,
            [
                ElementMapper::LAST_DOWNLOADED,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );

        $this->cacheService->set(
            $cacheKey,
            json_encode($data)
        );

        return $data;
    }
}

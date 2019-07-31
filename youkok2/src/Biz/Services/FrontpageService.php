<?php

namespace Youkok\Biz\Services;

use Illuminate\Support\Collection;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Common\Models\Session;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class FrontpageService
{
    const SERVICE_LIMIT = 10;

    const FRONTPAGE_PUT_DELTA_PARAM = 'delta';
    const FRONTPAGE_PUT_VALUE_PARAM = 'value';

    private $sessionService;

    private $popularCoursesProcessor;
    private $popularElementsProcessor;
    private $cacheService;
    private $elementService;
    private $courseService;
    private $downloadService;

    public function __construct(
        UserSessionService $sessionService,
        MostPopularCoursesService $popularCoursesProcessor,
        MostPopularElementsService $popularElementsProcessor,
        CacheService $cacheService,
        ElementService $elementService,
        CourseService $courseService,
        DownloadService $downloadService
    ) {
        $this->sessionService = $sessionService;
        $this->popularCoursesProcessor = $popularCoursesProcessor;
        $this->popularElementsProcessor = $popularElementsProcessor;
        $this->popularElementsProcessor = $popularElementsProcessor;
        $this->cacheService = $cacheService;
        $this->elementService = $elementService;
        $this->courseService = $courseService;
        $this->downloadService = $downloadService;
    }

    public function boxes(): array
    {
        // TODO move this into another service
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

    public function popularElements(): array
    {
        $session = $this->sessionService->getSession();

        return $this->popularElementsProcessor->fromDelta(
            $session->getMostPopularElement(),
            static::SERVICE_LIMIT
        );
    }

    public function popularCourses(): array
    {
        $session = $this->sessionService->getSession();

        return $this->popularCoursesProcessor->fromDelta(
            $session->getMostPopularCourse(),
            static::SERVICE_LIMIT
        );
    }

    public function newest(): array
    {
        return $this->elementService->getNewestElements(static::SERVICE_LIMIT);
    }

    public function lastVisited(): Collection
    {
        return $this->courseService->getLastVisitedCourses(static::SERVICE_LIMIT);
    }

    public function lastDownloaded(): array
    {
        return $this->downloadService->getLatestDownloads(static::SERVICE_LIMIT);
    }

    public function put(string $delta, string $value): array
    {
        if (!in_array($delta, [Session::KEY_MOST_POPULAR_ELEMENT, Session::KEY_MOST_POPULAR_COURSE])) {
            throw new InvalidRequestException();
        }

        if ($delta === Session::KEY_MOST_POPULAR_ELEMENT && !in_array($value, MostPopularElement::all())) {
            throw new InvalidRequestException();
        }

        if ($delta === Session::KEY_MOST_POPULAR_COURSE && !in_array($value, MostPopularCourse::all())) {
            throw new InvalidRequestException();
        }

        $session = $this->sessionService->getSession();

        if ($delta === Session::KEY_MOST_POPULAR_ELEMENT) {
            $session->setMostPopularElement($value);

            return $this->popularElementsProcessor->fromDelta($value, static::SERVICE_LIMIT);
        }

        $session->setMostPopularCourse($value);

        return $this->popularCoursesProcessor->fromDelta($value, static::SERVICE_LIMIT);
    }
}

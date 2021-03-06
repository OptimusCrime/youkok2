<?php
namespace Youkok\Biz\Services;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidFlagCombination;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class FrontpageService
{
    const SERVICE_LIMIT = 10;

    private MostPopularCoursesService $popularCoursesProcessor;
    private MostPopularElementsService $popularElementsProcessor;
    private CacheService $cacheService;
    private ElementService $elementService;
    private CourseService $courseService;
    private DownloadService $downloadService;

    public function __construct(
        MostPopularCoursesService $popularCoursesProcessor,
        MostPopularElementsService $popularElementsProcessor,
        CacheService $cacheService,
        ElementService $elementService,
        CourseService $courseService,
        DownloadService $downloadService
    ) {
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
     * @param MostPopularElement $delta
     * @return array
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    public function popularElements(MostPopularElement $delta): array
    {
        return $this->popularElementsProcessor->fromDelta(
            $delta->getValue(),
            static::SERVICE_LIMIT
        );
    }

    /**
     * @param MostPopularCourse $delta
     * @return array
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    public function popularCourses(MostPopularCourse $delta): array
    {
        return $this->popularCoursesProcessor->fromDelta(
            $delta->getValue(),
            static::SERVICE_LIMIT
        );
    }

    /**
     * @return array
     * @throws GenericYoukokException
     * @throws ElementNotFoundException
     * @throws InvalidFlagCombination
     */
    public function newest(): array
    {
        return $this->elementService->getNewestElements(static::SERVICE_LIMIT);
    }

    public function lastVisited(): array
    {
        return $this->courseService->getLastVisitedCourses();
    }

    /**
     * @return array
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    public function lastDownloaded(): array
    {
        return $this->downloadService->getLatestDownloads(static::SERVICE_LIMIT);
    }
}

<?php
namespace Youkok\Biz\Services\Download;

use Carbon\Carbon;
use Exception;
use RedisException;
use Youkok\Biz\Exceptions\InvalidValueException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class UpdateDownloadsService
{
    private CacheService $cacheService;
    private DownloadService $downloadService;
    private DownloadCacheSetService $downloadCacheSetService;

    public function __construct(CacheService $cacheService, DownloadService $downloadService, DownloadCacheSetService $downloadCacheSetService)
    {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
        $this->downloadCacheSetService = $downloadCacheSetService;
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    public function run(Element $element): void
    {
        $this->downloadService->addDatabaseDownload($element);

        $this->updateElementDownloads($element, false);

        $course = $element->getCourse();
        if ($course) {
            $this->updateElementDownloads($course, true);
            $this->flushDownloadForCourseInMostPopularSets();
        }

        $this->addAndFlushDownloadForElementInMostPopularSets($element);

        $this->updateTotalNumberOfDownloads();

        $this->cacheService->delete(CacheKeyGenerator::keyForLastDownloadedPayload());
    }

    private function updateElementDownloads(Element $element, bool $isCourse): void
    {
        $element->downloads_today += 1;
        $element->downloads_week += 1;
        $element->downloads_month += 1;
        $element->downloads_year += 1;
        $element->downloads_all += 1;
        if (!$isCourse) {
            $element->last_downloaded = Carbon::now();
        }

        $element->save();
    }

    /**
     * @throws RedisException
     * @throws InvalidValueException
     */
    private function addAndFlushDownloadForElementInMostPopularSets(Element $element): void
    {
        foreach (MostPopularElement::collection() as $delta) {
            // REMEMBER! Ensure that the cache set is created before increasing the value
            $this->downloadCacheSetService->createMostPopularElementsForDeltaCacheIfNecessary($delta);

            $key = CacheKeyGenerator::keyForMostPopularElementsSetForDelta($delta);

            $this->cacheService->updateValueInSet($key, 1, (string) $element->id);

            $this->cacheService->delete(CacheKeyGenerator::keyForMostPopularElementsForDelta($delta));
        }
    }

    /**
     * @throws RedisException
     */
    private function flushDownloadForCourseInMostPopularSets(): void
    {
        foreach (MostPopularCourse::collection() as $delta) {
            $this->cacheService->delete(CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta));
        }
    }

    /**
     * @throws RedisException
     */
    private function updateTotalNumberOfDownloads(): void
    {
        $numberOfDownloads = $this->cacheService->get(CacheKeyGenerator::keyForTotalNumberOfDownloads());

        if ($numberOfDownloads !== null) {
            $newTotalNumberOfDownloads = ((int) $numberOfDownloads) + 1;

            $this->cacheService->set(
                CacheKeyGenerator::keyForTotalNumberOfDownloads(),
                (string) $newTotalNumberOfDownloads
            );
        }
    }
}

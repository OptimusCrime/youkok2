<?php
namespace Youkok\Biz\Services\Download;

use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularElement;

class UpdateDownloadsService
{
    private $cacheService;
    private $downloadService;

    public function __construct(CacheService $cacheService, DownloadService $downloadService)
    {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
    }

    public function run(Element $element)
    {
        // Add the download to the database first
        $this->downloadService->newDownloadForElement($element);

        // Update the number of downloads in the cache for this particular element
        $this->addDownloadForElement($element);

        // Update this elements downloads in the most popular sets
        $this->addDownloadForElementInMostPopularSets($element);

        // Update the total number of downloads
        $this->updateTotalNumberOfDownloads();

        // Flush the payload cache
        $this->cacheService->delete(CacheKeyGenerator::keyForLastDownloadedPayload());
    }

    private function addDownloadForElement(Element $element)
    {
        $downloads = $this->cacheService->getDownloadsForId($element->id);

        if ($downloads === null) {
            // Unable to find number of downloads from the cache (it could be zero), so fetch it from the DB
            $databaseDownloads = $this->downloadService->getDownloadsForId($element->id);
            if ($databaseDownloads !== null) {
                // This element has downloads, but the cache was empty, update the cache
                $this->cacheService->setDownloadsForId($element->id, $databaseDownloads);
            } else {
                // This means that the number of downloads is in fact zero, set zero to the cache. This could
                // also be caught by the guard in CacheService::increaseDownloadsForId, but I think this is
                // cleaner.
                $this->cacheService->setDownloadsForId($element->id, (int) $databaseDownloads);
            }
        }

        $this->cacheService->increaseDownloadsForId($element->id);
    }

    private function addDownloadForElementInMostPopularSets(Element $element)
    {
        foreach (MostPopularElement::all() as $delta) {
            $setKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

            $this->cacheService->updateValueInSet($setKey, 1, $element->id);
        }
    }

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

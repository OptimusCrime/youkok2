<?php
namespace Youkok\Biz\Services\PopularListing;

use Monolog\Logger;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularElement;

class MostPopularElementsService implements MostPopularInterface
{
    private $cacheService;
    private $downloadService;
    private $elementService;
    private $logger;

    public function __construct(
        CacheService $cacheService,
        DownloadService $downloadService,
        ElementService $elementService,
        Logger $logger
    ) {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
        $this->elementService = $elementService;
        $this->logger = $logger;
    }

    public function fromDelta(string $delta, int $limit): array
    {
        $ids = $this->cacheService->getMostPopularElementsFromDelta($delta, $limit);
        if (empty($ids)) {
            // If this response was empty, it means that the cache is empty, try to refresh it
            $this->refreshForDelta($delta);

            // Run the fetch again, and hope we are more lucky this time around
            return static::idListToElements($this->cacheService->getMostPopularElementsFromDelta($delta, $limit));
        }

        return static::idListToElements($ids);
    }

    public function refresh(): void
    {
        $this->cacheService->clearCacheForMostPopularKeys(MostPopularElement::all());

        foreach (MostPopularElement::all() as $key) {
            $this->refreshForDelta($key);
        }
    }

    private function refreshForDelta(string $delta): void
    {
        $elements = $this->downloadService->getMostPopularElementsFromDelta($delta, 100);
        $setKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

        foreach ($elements as $element) {
            if ($element->deleted === 1 || $element->pending === 1) {
                continue;
            }

            $this->cacheService->insertIntoSet($setKey, (int) $element->download_count, (string) $element->id);
        }
    }

    private function idListToElements(array $ids): array
    {
        $elements = [];
        foreach ($ids as $id => $downloads) {
            try {
                $element = $this->elementService->getElement(
                    new SelectStatements('id', $id),
                    ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'checksum', 'directory'],
                    [
                        ElementService::FLAG_ENSURE_VISIBLE
                    ]
                );

                $element->setDownloads($downloads);

                $elements[] = $element;
            } catch (ElementNotFoundException $ex) {
                $this->logger->warning($ex);
            }
        }
        return $elements;
    }
}

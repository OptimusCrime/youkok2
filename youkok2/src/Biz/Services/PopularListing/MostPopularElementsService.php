<?php
namespace Youkok\Biz\Services\PopularListing;

use Youkok\Biz\Services\CacheService;
use Youkok\Common\Controllers\DownloadController;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularElement;

class MostPopularElementsService implements MostPopularInterface
{
    private $cacheService;

    public function __construct(CacheService $cacheService) {
        $this->cacheService = $cacheService;
    }

    public function fromDelta(string $delta, int $limit)
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
        $this->cacheService->clearCacheForKeys(MostPopularElement::all());

        foreach (MostPopularElement::all() as $key) {
            $this->refreshForDelta($key);
        }
    }

    private function refreshForDelta(string $delta): void
    {
        $elements = DownloadController::getMostPopularElementsFromDelta($delta);
        $setKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

        foreach ($elements as $element) {
            // TODO: fix casting here. Weird method signature
            $this->cacheService->insertIntoSet($setKey, $element->download_count, $element->id);
        }
    }

    private static function idListToElements(array $ids): array
    {
        $elements = [];
        foreach ($ids as $id => $downloads) {
            $element = Element::fromIdVisible($id, ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'checksum']);
            $element->setDownloads($downloads);

            $elements[] = $element;
        }
        return $elements;
    }
}

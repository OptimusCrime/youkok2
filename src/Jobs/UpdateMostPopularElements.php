<?php
namespace Youkok\Jobs;

use Youkok\CachePopulators\PopulateMostPopularElements;
use Youkok\Enums\MostPopularElement;
use Youkok\Utilities\CacheKeyGenerator;

class UpdateMostPopularElements extends JobInterface
{
    private static $mostPopularKeys = [
        MostPopularElement::TODAY,
        MostPopularElement::WEEK,
        MostPopularElement::MONTH,
        MostPopularElement::YEAR,
        MostPopularElement::ALL,
    ];

    public function run()
    {
        $this->clearCache();
        $this->populateCache();
    }

    private function clearCache()
    {
        $cache = $this->containers->get('cache');
        if ($cache === null) {
            return null;
        }

        foreach (static::$mostPopularKeys as $key) {
            $cacheKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($key);
            $cache->delete($cacheKey);
        }
    }

    private function populateCache()
    {
        $cache = $this->containers->get('cache');
        if ($cache === null) {
            return null;
        }

        foreach (static::$mostPopularKeys as $key) {
            PopulateMostPopularElements
                ::setCache($cache)
                ->withDelta($key)
                ->run();
        }
    }
}

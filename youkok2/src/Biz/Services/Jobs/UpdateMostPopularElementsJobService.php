<?php
namespace Youkok\Biz\Services\Jobs;

use Redis;

use Youkok\CachePopulators\PopulateMostPopularElements;
use Youkok\Enums\MostPopularElement;
use Youkok\Common\Utilities\CacheKeyGenerator;

class UpdateMostPopularElementsJobService implements JobServiceInterface
{
    /** @var \Redis */
    private $cache;

    /** @var \Youkok\CachePopulators\PopulateMostPopularElements */
    private $populateMostPopularElements;

    public function __construct(Redis $cache, PopulateMostPopularElements $populateMostPopularElements)
    {
        $this->cache = $cache;
        $this->populateMostPopularElements = $populateMostPopularElements;
    }

    public function run()
    {
        $this->clearCache();
        $this->populateCache();
    }

    private function clearCache()
    {
        $cache = $this->cache;
        if ($cache === null) {
            return null;
        }

        foreach (MostPopularElement::all() as $key) {
            $cacheKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($key);
            $cache->delete($cacheKey);
        }
    }

    private function populateCache()
    {
        $cache = $this->cache;
        if ($cache === null) {
            return null;
        }

        foreach (MostPopularElement::all() as $key) {
            $this->populateMostPopularElements->run($key);
        }
    }
}

<?php
namespace Youkok\CachePopulators;

use Redis;

use Youkok\Common\Controllers\DownloadController;
use Youkok\Common\Utilities\CacheKeyGenerator;

class PopulateMostPopularElements
{
    private $cache;

    public function __construct(Redis $cache)
    {
        $this->cache = $cache;
    }

    public function run($delta)
    {
        $elements = DownloadController::getMostPopularElementsFromDelta($delta);
        $setKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);
        $this->insertElementsToCache($this->cache, $setKey, $elements);
    }

    private function insertElementsToCache($setKey, $elements)
    {
        foreach ($elements as $element) {
            $this->cache->zadd($setKey, $element->download_count, $element->id);
        }
    }
}

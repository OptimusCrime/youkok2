<?php
namespace Youkok\CachePopulators;

use Youkok\Controllers\DownloadController;
use Youkok\Utilities\CacheKeyGenerator;

class PopulateMostPopularElements extends AbstractCachePopulator
{
    private $delta;

    public static function setCache($cache)
    {
        return new PopulateMostPopularElements($cache);
    }

    public function withDelta($delta)
    {
        $this->delta = $delta;
        return $this;
    }

    public function run()
    {
        $elements = DownloadController::getMostPopularElementsFromDelta($this->delta);
        $setKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($this->delta);

        static::insertElementsToCache($this->cache, $setKey, $elements);
    }

    private static function insertElementsToCache($cache, $setKey, $elements)
    {
        foreach ($elements as $element) {
            $cache->zadd($setKey, $element->download_count, $element->id);
        }
    }
}

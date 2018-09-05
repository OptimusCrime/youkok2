<?php
namespace Youkok\Biz\Services\Cache;

use Redis;

use Youkok\CachePopulators\PopulateMostPopularElements;
use Youkok\Biz\Services\PopularListing\PopularCoursesService;
use Youkok\Common\Utilities\CacheKeyGenerator;

class CacheService
{
    private $cache;
    private $settings;
    private $populateMostPopularElements;

    public function __construct(Redis $cache, PopulateMostPopularElements $populateMostPopularElements) {
        $this->cache = $cache;
        $this->populateMostPopularElements = $populateMostPopularElements;
    }

    public function getMostPopularElementsFromDelta($delta, $limit)
    {
        $key = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

        $result = $this->getSortedRangeByKey($key, $limit);
        if (!empty($result)) {
            return $result;
        }

        $this->populateMostPopularElements->run($delta);

        // Attempt to fetch the result again
        return static::getSortedRangeByKey($key, $limit);
    }

    public function getMostPopularCoursesFromDelta($delta)
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        $result = $this->getCacheByKey($key);

        if (empty($result)) {
            return static::getMostPopularCoursesFromDisk($key);
        }

        return $result;
    }

    // TODO remove this? URI is also stored in the database...
    public function getElementFromUri($uri)
    {
        $key = CacheKeyGenerator::keyForElementUri($uri);
        return $this->getCacheByKey($key);
    }

    public function setByKey($key, $value)
    {
        if ($this->cache !== null) {
            $this->cache->set($key, $value);
        }
    }

    public function getDonwloadsForId($id)
    {
        if ($this->cache === null) {
            return null;
        }

        return $this->cache->get(CacheKeyGenerator::keyForElementDownloads($id));
    }

    public function setDownloadsForId($id, $downloads)
    {
        if ($this->cache === null) {
            return null;
        }

        $this->setByKey(CacheKeyGenerator::keyForElementDownloads($id), $downloads);
    }

    private function getMostPopularCoursesFromDisk($key)
    {
        $cacheDirectoryKey = $this->settings[PopularCoursesService::CACHE_DIRECTORY_KEY];
        $cacheDirectory = $cacheDirectoryKey . PopularCoursesService::CACHE_DIRECTORY_SUB;
        $cacheFile = $cacheDirectory . DIRECTORY_SEPARATOR . $key . '.json';

        $fileContents = file_get_contents($cacheFile);
        if ($fileContents === null or strlen($fileContents) === 0) {
            return [];
        }

        // We return the entire JSON string here.
        return $fileContents;
    }

    private function getSortedRangeByKey($key, $limit, $offset = 0)
    {
        if ($this->cache === null) {
            return [];
        }

        return $this->cache->zRevRangeByScore($key, '+inf', '-inf', [
            'limit' => [
                $offset,
                $limit
            ],
            'withscores' => true
        ]);
    }

    private function getCacheByKey($key)
    {
        if ($this->cache === null) {
            return [];
        }

        return $this->cache->get($key);
    }
}

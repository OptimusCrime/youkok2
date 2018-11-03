<?php
namespace Youkok\Biz\Services;

use Redis;

use Youkok\Common\Utilities\CacheKeyGenerator;

class CacheService
{
    private $cache;

    public function __construct(Redis $cache) {
        $this->cache = $cache;
    }

    public function getMostPopularElementsFromDelta($delta, $limit)
    {
        $key = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

        return $this->getSortedRangeByKey($key, $limit);
    }

    public function getMostPopularCoursesFromDelta($delta)
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        return $this->getCacheByKey($key);
    }

    public function setByKey($key, $value)
    {
        if ($this->cache !== null) {
            $this->cache->set($key, $value);
        }
    }

    public function insertIntoSet($setKey, $value, $id)
    {
        $this->cache->zadd($setKey, $value, $id);
    }

    public function updateValueInSet($setKey, $increase, $id)
    {
        $this->cache->zIncrBy($setKey, $increase, $id);
    }

    public function getDownloadsForId($id)
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

    public function increaseDownloadsForId($id)
    {
        $downloads = $this->getDownloadsForId($id);

        // This is just a guard, and should never have to happen
        if ($downloads === null || $downloads === false) {
            $downloads = 0;
        }

        $this->setDownloadsForId($id, $downloads + 1);
    }

    public function clearCacheForKeys(array $keys)
    {
        if ($this->cache === null) {
            return null;
        }

        foreach ($keys as $key) {
            $cacheKey = CacheKeyGenerator::keyForMostPopularElementsForDelta($key);
            $this->cache->delete($cacheKey);
        }
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

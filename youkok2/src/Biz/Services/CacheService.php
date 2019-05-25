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

    public function getMostPopularElementsFromDelta(string $delta, int $limit)
    {
        $key = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

        return $this->getSortedRangeByKey($key, $limit);
    }

    public function getMostPopularCoursesFromDelta(string $delta)
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

    public function getDownloadsForId(int $id): ?int
    {
        if ($this->cache === null) {
            return null;
        }

        $downloads = $this->cache->get(CacheKeyGenerator::keyForElementDownloads($id));

        // Redis returns false for null values for unknown reasons
        if ($downloads === false) {
            return null;
        }

        return (int) $downloads;
    }

    public function setDownloadsForId(int $id, int $downloads): void
    {
        if ($this->cache === null) {
            return null;
        }

        $this->setByKey(CacheKeyGenerator::keyForElementDownloads($id), $downloads);
    }

    public function increaseDownloadsForId(int $id): void
    {
        $downloads = $this->getDownloadsForId($id);

        // This is just a guard, and should never have to happen
        if ($downloads === null) {
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

    private function getSortedRangeByKey(string $key, int $limit, int $offset = 0): array
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

    // TODO: WTF, this methods returns string but also array??
    private function getCacheByKey($key)
    {
        if ($this->cache === null) {
            return [];
        }

        return $this->cache->get($key);
    }
}

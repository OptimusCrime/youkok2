<?php
namespace Youkok\Biz\Services;

use Redis;

use Youkok\Common\Utilities\CacheKeyGenerator;

class CacheService
{
    private $cache;

    public function __construct(Redis $cache)
    {
        $this->cache = $cache;
    }

    public function getMostPopularElementsFromDelta(string $delta, int $limit)
    {
        $key = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

        return $this->getSortedRangeByKey($key, $limit);
    }

    public function getMostPopularCoursesFromDelta(string $delta): ?string
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        return $this->getCacheByKey($key);
    }

    public function get(string $key): ?string
    {
        // TODO ?
        return $this->getCacheByKey($key);
    }

    public function set(string $key, string $value): void
    {
        if ($this->cache !== null) {
            $this->cache->set($key, $value);
        }
    }

    public function insertIntoSet($setKey, $value, $id): void
    {
        // TODO: method is complaning?
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
            return;
        }

        $this->set(CacheKeyGenerator::keyForElementDownloads($id), (string) $downloads);
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

    public function clearCacheForKeys(array $keys): void
    {
        if ($this->cache === null) {
            return;
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

    private function getCacheByKey($key): ?string
    {
        if ($this->cache === null) {
            return '';
        }

        $data = $this->cache->get($key);

        // Redis returns false if no data was found
        if ($data === false) {
            return null;
        }

        return $data;
    }
}

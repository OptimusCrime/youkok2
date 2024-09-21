<?php
namespace Youkok\Biz\Services;

use Redis;

use RedisException;
use Youkok\Common\Utilities\CacheKeyGenerator;

class CacheService
{
    private Redis $cache;

    public function __construct(Redis $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @throws RedisException
     */
    public function getMostPopularElementsFromDelta(string $delta, int $limit): array
    {
        $key = CacheKeyGenerator::keyForMostPopularElementsForDelta($delta);

        return $this->getSortedRangeByKey($key, $limit);
    }

    /**
     * @throws RedisException
     */
    public function getMostPopularCoursesFromDelta(string $delta): ?string
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        return $this->getCacheByKey($key);
    }

    /**
     * @throws RedisException
     */
    public function get(string $key): ?string
    {
        return $this->getCacheByKey($key);
    }

    /**
     * @throws RedisException
     */
    public function set(string $key, string $value): bool
    {
        return $this->cache->set($key, $value);
    }

    /**
     * @throws RedisException
     */
    public function delete(string $key): void
    {
        $this->cache->del($key);
    }

    /**
     * @throws RedisException
     */
    public function getAllKeys(): array
    {
        return $this->cache->keys('*');
    }

    /**
     * @throws RedisException
     */
    public function insertIntoSet(string $setKey, int $value, string $id): void
    {
        $this->cache->zadd($setKey, $value, $id);
    }

    /**
     * @throws RedisException
     */
    public function removeFromSetByRank(string $setKey, int $start, int $end): void
    {
        $this->cache->zRemRangeByRank($setKey, $start, $end);
    }

    /**
     * @throws RedisException
     */
    public function updateValueInSet($setKey, $increase, $id): void
    {
        $this->cache->zIncrBy($setKey, $increase, $id);
    }

    /**
     * @throws RedisException
     */
    public function getDownloadsForId(int $id): ?int
    {
        $downloads = $this->cache->get(CacheKeyGenerator::keyForElementDownloads($id));

        // Redis returns false for null values for unknown reasons
        if ($downloads === false) {
            return null;
        }

        return (int) $downloads;
    }

    /**
     * @throws RedisException
     */
    public function setDownloadsForId(int $id, int $downloads): void
    {
        $this->set(CacheKeyGenerator::keyForElementDownloads($id), (string) $downloads);
    }

    /**
     * @throws RedisException
     */
    public function increaseDownloadsForId(int $id): void
    {
        $downloads = $this->getDownloadsForId($id);

        // This is just a guard, and should never have to happen
        if ($downloads === null) {
            $downloads = 0;
        }

        $this->setDownloadsForId($id, $downloads + 1);
    }

    /**
     * @throws RedisException
     */
    public function getSortedRangeByKey(string $key, int $limit = null, int $offset = 0): array
    {
        if ($limit === null) {
            return $this->cache->zRevRangeByScore($key, '+inf', '-inf', [
                'withscores' => true
            ]);
        }

        return $this->cache->zRevRangeByScore($key, '+inf', '-inf', [
            'limit' => [
                $offset,
                $limit
            ],
            'withscores' => true
        ]);
    }

    /**
     * @throws RedisException
     */
    private function getCacheByKey(string $key): ?string
    {
        $data = $this->cache->get($key);

        // Redis returns false if no data was found
        if ($data === false) {
            return null;
        }

        return $data;
    }
}

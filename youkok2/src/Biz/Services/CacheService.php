<?php
namespace Youkok\Biz\Services;

use Redis;

use RedisException;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

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
    public function getMostPopularElementsSetFromDelta(MostPopularElement $delta): array
    {
        $key = CacheKeyGenerator::keyForMostPopularElementsSetForDelta($delta);
        return $this->getSortedRangeByKey($key);
    }

    /**
     * @throws RedisException
     */
    public function getMostPopularCoursesSetFromDelta(MostPopularCourse $delta): array
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesSetForDelta($delta);
        return $this->getSortedRangeByKey($key);
    }

    /**
     * @throws RedisException
     */
    public function get(string $key): ?string
    {
        return $this->getCacheByKey($key);
    }

    /**
     * Stores for one hour by default.
     *
     * @throws RedisException
     */
    public function set(string $key, string $value, int $expireMs = 60 * 60 * 1000): bool
    {
        return $this->cache->setex($key, $expireMs, $value);
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
    public function updateValueInSet($setKey, $increase, $id): void
    {
        $this->cache->zIncrBy($setKey, $increase, $id);
    }

    /**
     * @throws RedisException
     */
    public function getSortedRangeByKey(string $key): array
    {
        return $this->cache->zRevRangeByScore($key, '+inf', '-inf', [
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
